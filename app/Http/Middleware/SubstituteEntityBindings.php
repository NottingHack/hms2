<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Illuminate\Contracts\Routing\Registrar;

class SubstituteEntityBindings
{
    /**
     * The router instance.
     *
     * @var \Illuminate\Contracts\Routing\Registrar
     */
    protected $router;

    /**
     * The EntityManager instance.
     *
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * Create a new bindings substitutor.
     *
     * @param  \Illuminate\Contracts\Routing\Registrar  $router
     * @return void
     */
    public function __construct(Registrar $router, EntityManagerInterface $em)
    {
        $this->router = $router;
        $this->em = $em;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $route = $request->route();

        $this->router->substituteBindings($route);

        $this->router->substituteImplicitBindings($route);

        $parameters = $route->parameters();
        $entityNamespaces = config('doctrine.managers.default.namespaces');

        /** @var \ReflectionParameter $parameter */
        foreach ($route->signatureParameters() as $parameter) {
            if ( ! ($class = $parameter->getClass())) {
                continue;
            }
            if ($class->isSubclassOf(Model::class)) {
                continue;
            }
            if ( ! in_array($class->getNamespaceName(), $entityNamespaces)) {
                continue;
            }

            $id = $parameters[$parameter->name];
            $entity = $this->em->find($class->name, $id);

            if ( ! $entity && ! $parameter->isDefaultValueAvailable()) {
                throw EntityNotFoundException::fromClassNameAndIdentifier(
                    $class->name,
                    [$id]
                 );
            }

            $route->setParameter($parameter->name, $entity);
        }

        return $next($request);
    }
}
