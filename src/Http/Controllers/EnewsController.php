<?php

namespace Jzpeepz\Enews\Http\Controllers;

use Jzpeepz\Enews\Enews;
use Jzpeepz\Adestra\AdestraCampaign;
use Illuminate\Routing\Controller;
use App\Models\Publish\Article;

class EnewsController extends Controller
{
    public function __construct()
    {
        // get current site
        $this->site = config('site');

        // get the root page (homepage)
        $root = \Page::getRoot();

        // share the root page so we can use it in all views
        view()->share('root', $root);

        // set current site for use in various places
        view()->share('current_site', $this->site);
        \Config::set('site', $this->site);
    }

    public function index()
    {
        $enews = Enews::orderBy('id', 'desc')->paginate(25);

        return view('enews::' . config('enews.theme') . '.index', compact('enews'));
    }

    public function create()
    {
        $formOptions = [
            'action' => route('jzpeepz.enews.store'),
            'method' => 'post',
        ];

        $email = new Enews;
        $email->send_at = date('n/j/Y');

        $articles = Article::find([
            'tags' => 15823,
            'pageSize' => 50,
        ]);

        return view('enews::' . config('enews.theme') . '.form', compact('formOptions', 'email', 'articles'));
    }

    public function store()
    {
        $data = request()->except('_token');

        $email = new Enews;
        $email->fill($data);
        $email->setContent();
        $email->save();

        session()->put('alert-success', 'Enews created successfully!');

        return redirect()->route('jzpeepz.enews.preview', $email->id);
    }

    public function edit($id)
    {
        $formOptions = [
            'action' => route('jzpeepz.enews.update', $id),
            'method' => 'put',
        ];

        $email = Enews::find($id);

        $articles = Article::find([
            'tags' => 15823,
            'pageSize' => 50,
        ]);

        return view('enews::' . config('enews.theme') . '.form', compact('formOptions', 'email', 'articles'));
    }

    public function update($id)
    {
        $data = request()->except('_token');

        $email = Enews::find($id);
        $email->fill($data);
        $email->setContent();
        $email->save();

        session()->put('alert-success', 'Enews updated successfully!');

        return redirect()->route('jzpeepz.enews.preview', $email->id);
    }

    public function destroy($id)
    {
    }

    public function preview($id)
    {
        $email = Enews::find($id);

        return view('enews::' . config('enews.theme') . '.preview', compact('email'));
    }

    public function html($id)
    {
        $email = Enews::find($id);

        return $email->html;
    }

    public function text($id)
    {
        $email = Enews::find($id);

        return $email->text;
    }

    public function send($id)
    {
        $email = Enews::find($id);

        $list_id = $_POST['list_id'];
        $scheduled_for = isset($_POST['scheduled_for']) ? $_POST['scheduled_for'] : null;
        $testEmails = isset($_POST['testEmails']) && !empty($_POST['testEmails']) ? $_POST['testEmails'] : null;
        $launchOptions = [];

        if (!empty($scheduled_for)) {
            $launchOptions['date_scheduled'] = date('c', (strtotime($scheduled_for)));
        }

        $debug = false;

        // use the existing campaign if available
        if (!empty($email->campaign_id)) {
            // echo 'Updating campaign...';
            $campaign = AdestraCampaign::find($email->campaign_id, $debug)
                ->update([
                    'name' => 'Greenhead Enews ' . date('n/j/Y'),
                    'description' => $email->subject . ' ' . date('n/j/Y'),
                    'list_id' => $list_id,
                ]);
        } else {
            $campaign = AdestraCampaign::make([
                'name' => 'Greenhead Enews ' . date('n/j/Y'),
                'description' => $email->subject . ' ' . date('n/j/Y'),
                'project_id' => $email->getProjectId(),
                'list_id' => $list_id,
            ], $debug)
            ->create();
        }

        $campaign->setAllOptions([
            'subject_line' => $email->subject,
            'domain' => 'email.greenhead.net',
            'from_prefix' => 'mail',
            'from_name' => 'Greenhead',
            'auto_tracking' => 1,
            'user_from' => 1,
            'from_address' => 'enews@greenhead.net',
            'user_reply' => 1,
            'reply_address' => 'enews@greenhead.net',
            'reply_name' => 'Greenhead',
            'unsub_list' => $email->getUnsubListId(),
        ])
        ->setMessage('html', $email->html)
        ->setMessage('text', $email->text)
        ->publish();

        if (!empty($testEmails)) {
            // send test
            $response = $campaign->sendTest($testEmails);

            if ($response->isError()) {
                echo '<pre>' . print_r($response, true) . '</pre>';
                exit;
            }

            // Flash::set('success', 'Test sent successfully!');
            session()->put('alert-success', 'Test sent successfully!');
            $email->campaign_id = $campaign->id;
            $email->save();
        } else {
            // launch
            $response = $campaign->launch($launchOptions);

            if ($response->isError()) {
                session()->put('alert-danger', 'Enews was not sent!');
            } else {
                if (!empty($scheduled_for)) {
                    session()->put('alert-success', 'Enews was scheduled to send at ' . date('l, F j, Y g:i a', strtotime($scheduled_for)) . '!');
                } else {
                    session()->put('alert-success', 'Enews was sent successfully!');
                }
                $email->sent_at = date('Y-m-d H:i:s');
                $email->campaign_id = $campaign->id;
                $email->save();
            }
        }

        return redirect()->route('jzpeepz.enews.preview', $email->id);
    }
}
