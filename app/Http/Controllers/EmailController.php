<?php

namespace App\Http\Controllers;

use App\Jobs\EmailCurrentMembersJob;
use App\Mail\ToCurrentMembers;
use HMS\Entities\Role;
use HMS\Repositories\RoleRepository;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class EmailController extends Controller
{
    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * Create a new controller instance.
     *
     * @param RoleRepository $roleRepository
     */
    public function __construct(
        RoleRepository $roleRepository
    ) {
        $this->roleRepository = $roleRepository;

        $this->middleware('feature:email_all_members');
        $this->middleware('can:email.allMembers');
    }

    /**
     * Show the draft email view.
     *
     * @return \Illuminate\Http\Response
     */
    public function draft()
    {
        $draft = Cache::get('emailMembers.draft', [
            'subject' => '',
            'emailContent' => '',
            'recipients' => [],
        ]);

        $roles = array_filter($this->roleRepository->findAll(), function ($role) {
            $name = $role->getName();

            return $name === Role::MEMBER_CURRENT || str_starts_with($name, 'tools.') || str_starts_with($name, 'team.');
        });

        return view('emailMembers.draft', $draft)
            ->with([
                'roles' => $roles,
            ]);
    }

    /**
     * Clear draft cache.
     *
     * @return \Illuminate\Http\Response
     */
    public function forget()
    {
        Cache::forget('emailMembers.draft');

        return redirect()->route('email-members.draft');
    }

    /**
     * Store the draft email and return a preview.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function review(Request $request)
    {
        Cache::put('emailMembers.draft', [
            'subject' => $request->subject,
            'emailContent' => $request->emailContent,
            'recipients' => $request->recipients,
        ], now()->addMinutes(30));

        $emailView = new ToCurrentMembers($request->subject, $request->emailContent);
        $renderedTextPlain = $emailView->renderText();

        $roles = array_map(function ($roleName) {
            return $this->roleRepository->findOneByName($roleName);
        }, $request->recipients);

        $currentMemberRole = $this->roleRepository->findOneByName(Role::MEMBER_CURRENT);

        $users = [];
        foreach ($roles as $role) {
            $roleMembers = $role->getUsers();

            // We should check that they are a current member, for cases where a role is retained.
            foreach ($roleMembers as $roleMember) {
                if ($roleMember->getRoles()->contains($currentMemberRole)) {
                    $users[] = $roleMember;
                }
            }
        }
        $users = array_unique($users, SORT_REGULAR);

        return view('emailMembers.review')
            ->with([
                'subject' => $request->subject,
                'emailPlain' => $renderedTextPlain,
                'currentMemberCount' => count($users),
            ]);
    }

    /**
     * Show a html review of the draft email.
     *
     * @param ViewFactory $viewFactory
     * @param CssToInlineStyles $cssToInlineStyles
     *
     * @return \Illuminate\Http\Response
     */
    public function reviewHtml(ViewFactory $viewFactory, CssToInlineStyles $cssToInlineStyles)
    {
        $draft = Cache::get('emailMembers.draft', [
            'subject' => '',
            'emailContent' => '',
            'recipients' => [],
        ]);

        $emailView = new ToCurrentMembers($draft['subject'], $draft['emailContent']);
        $renderedHtml = $emailView->render();
        // TODO:  build theme string from config
        $renderedHtmlCSS = new HtmlString(
            $cssToInlineStyles->convert(
                $renderedHtml,
                $viewFactory->make('vendor.mail.html.themes.' . config('mail.markdown.theme', 'default'))->render()
            )
        );

        return response($renderedHtmlCSS);
    }

    /**
     * Send the email.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(Request $request)
    {
        $draft = Cache::get('emailMembers.draft');
        $roleNames = $draft['recipients'];

        EmailCurrentMembersJob::dispatch($draft['subject'], $draft['emailContent'], $roleNames, $request->testSend);

        if (! $request->testSend) {
            flash('Email queued for sending', 'success');
            Cache::forget('emailMembers.draft');
        } else {
            flash('Test email queued for sending', 'success');
        }

        return redirect()->route('email-members.draft');
    }
}
