<?php

namespace App\Http\Controllers;

use App\Jobs\EmailCurrentMembersJob;
use App\Mail\ToCurrentMembers;
use HMS\Entities\Role;
use HMS\Repositories\RoleRepository;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\Request;
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
        $draft = \Cache::get('emailMembers.draft', [
            'subject' => '',
            'emailContent' => '',
        ]);

        return view('emailMembers.draft', $draft);
    }

    /**
     * Clear draft cache
     *
     * @return \Illuminate\Http\Response
     */
    public function forget()
    {
        \Cache::forget('emailMembers.draft');

        return redirect()->route('email-members.draft');
    }

    /**
     * Store the draft email and return a preview.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function review(Request $request)
    {
        \Cache::put('emailMembers.draft', [
            'subject' => $request->subject,
            'emailContent' => $request->emailContent,
        ], now()->addMinutes(30));

        $emailView = new ToCurrentMembers($request->subject, $request->emailContent);
        $renderedTextPlain = $emailView->renderText();

        $currentMemberCount = $this->roleRepository->findOneByName(Role::MEMBER_CURRENT)->getUsers()->count();

        return view('emailMembers.review')
            ->with([
                'subject' => $request->subject,
                'emailPlain' => $renderedTextPlain,
                'currentMemberCount' => $currentMemberCount,
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
        $draft = \Cache::get('emailMembers.draft', [
            'subject' => '',
            'emailContent' => '',
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
     * @param Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(Request $request)
    {
        $draft = \Cache::get('emailMembers.draft');

        EmailCurrentMembersJob::dispatch($draft['subject'], $draft['emailContent'], $request->testSend);

        if (! $request->testSend) {
            flash('Email queued for sending', 'success');
            \Cache::forget('emailMembers.draft');
        } else {
            flash('Test email queued for sending', 'success');
        }

        return redirect()->route('email-members.draft');
    }
}
