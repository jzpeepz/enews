<?php

namespace Jzpeepz\Enews;

use Pelago\Emogrifier;
use Illuminate\Database\Eloquent\Model;

class Enews extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at', 'sent_at'];

    public function setArticlesAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['articles'] = implode(',', $value);
        }
    }

    public function getArticlesAttribute($value)
    {
        if (empty($value)) {
            return [];
        }

        return explode(',', $value);
    }

    public function setSendAtAttribute($value)
    {
        $this->attributes['send_at'] = date('Y-m-d H:i:s', strtotime($value));
    }

    public function getSendAtAttribute($value)
    {
        if (empty($value)) {
            return date('n/j/Y');
        }

        return date('n/j/Y', strtotime($value));
    }

    public function setContent()
    {
        $this->html = view('enews::emails.' . $this->template . '.html', ['email' => $this])->render();
        $this->inlineCssIntoHtml();
        $this->text = view('enews::emails.' . $this->template . '.text', ['email' => $this])->render();
    }

    public static function routes()
    {
        \Route::get('/enews', '\Jzpeepz\Enews\Http\Controllers\EnewsController@index')->name('jzpeepz.enews.index');
        \Route::get('/enews/create', '\Jzpeepz\Enews\Http\Controllers\EnewsController@create')->name('jzpeepz.enews.create');
        \Route::post('/enews', '\Jzpeepz\Enews\Http\Controllers\EnewsController@store')->name('jzpeepz.enews.store');
        \Route::get('/enews/{id}/edit', '\Jzpeepz\Enews\Http\Controllers\EnewsController@edit')->name('jzpeepz.enews.edit');
        \Route::put('/enews/{id}', '\Jzpeepz\Enews\Http\Controllers\EnewsController@update')->name('jzpeepz.enews.update');
        \Route::delete('/enews/{id}', '\Jzpeepz\Enews\Http\Controllers\EnewsController@destroy')->name('jzpeepz.enews.destroy');

        \Route::get('/enews/{id}/preview', '\Jzpeepz\Enews\Http\Controllers\EnewsController@preview')->name('jzpeepz.enews.preview');
        \Route::post('/enews/{id}/send', '\Jzpeepz\Enews\Http\Controllers\EnewsController@send')->name('jzpeepz.enews.send');

        \Route::get('/enews/{id}/html', '\Jzpeepz\Enews\Http\Controllers\EnewsController@html')->name('jzpeepz.enews.html');
        \Route::get('/enews/{id}/text', '\Jzpeepz\Enews\Http\Controllers\EnewsController@text')->name('jzpeepz.enews.text');
    }

    public function getBanner($name)
    {
        $zone = $this->getZone();

        $config = $this->getConfig();

        $uniqueKey = "[*data('email')*]-" . $zone . '-' . date('YmdHis');

        if (isset($config['banners'])) {
            $banners = [];

            foreach ($config['banners'] as $bannerConfig) {
                $banners[$bannerConfig['name']] = '<a href="http://ABPG.nui.media/pipeline/' . $zone . '/0/cc?z=ABPG&pos=' . $bannerConfig['position'] . '&session=no&ajkey=' . $uniqueKey . '-007-' . $this->id . '"><img src="http://ABPG.nui.media/pipeline/' . $zone . '/0/vc?z=ABPG&dim=' . $bannerConfig['size'] . '&kw=&click=&pos=' . $bannerConfig['position'] . '&session=no&ajkey=' . $uniqueKey . '-007-' . $this->id . '" width="' . $bannerConfig['width'] . '" height="' . $bannerConfig['height'] . '" alt="" border="0"></a>';
            }
        } else {
            $banners = [
                'leaderboard' => '<a href="http://ABPG.nui.media/pipeline/' . $zone . '/0/cc?z=ABPG&pos=1&session=no&ajkey=' . $uniqueKey . '-007-' . $this->id . '"><img src="http://ABPG.nui.media/pipeline/' . $zone . '/0/vc?z=ABPG&dim=391&kw=&click=&pos=1&session=no&ajkey=' . $uniqueKey . '-007-' . $this->id . '" width="728" height="90" alt="" border="0"></a>',
                'box_1' => '<a class="adjuggler-ad" href="http://ABPG.nui.media/pipeline/' . $zone . '/0/cc?z=ABPG&pos=1&session=no&ajkey=' . $uniqueKey . '-005-' . $this->id . '"><img src="http://ABPG.nui.media/pipeline/' . $zone . '/0/vc?z=ABPG&dim=2123&kw=&click=&pos=1&session=no&ajkey=' . $uniqueKey . '-005-' . $this->id . '" width="300" height="250" alt="" border="0"></a>',
                'skyscraper' => '<a href="http://ABPG.nui.media/pipeline/' . $zone . '/0/cc?z=ABPG&pos=2&session=no&ajkey=' . $uniqueKey . '-006-' . $this->id . '"><img src="http://ABPG.nui.media/pipeline/' . $zone . '/0/vc?z=ABPG&dim=392&kw=&click=&pos=2&session=no&ajkey=' . $uniqueKey . '-006-' . $this->id . '" width="160" height="600" alt="" border="0"></a>',
            ];
        }

        return $banners[$name];
    }

    public function getZone()
    {
        $config = $this->getConfig();

        $zones = $config['zones'];

        return isset($zones[$this->day]) ? $zones[$this->day] : null;
    }

    public function getArticles()
    {
        $articles = [];

        $articleClass = Enews::getPublishArticleClass();

        foreach ($this->articles as $id) {
            $articles[] = $articleClass::find(['id' => $id]);
        }

        return collect($articles);
    }

    public function getGoogleTracker()
    {
        $zone = $this->getZone();

        return 'utm_source=enews_' . date('mdy') . '&utm_medium=email&utm_content=' . str_slug($this->subject) . '&utm_campaign=newsletter&enews_zone=' . $zone;
    }

    public function getProjectId()
    {
        return $this->getCampaignOption('project_id');
    }

    public function getFilterId()
    {
        return $this->getCampaignOption('filter_id');
    }

    public function getUnsubListId()
    {
        return $this->getCampaignOption('unsub_list');
    }

    public function inlineCssIntoHtml()
    {
        $css = '';
        $emogrifier = new Emogrifier($this->html, $css);
        $mergedHtml = @$emogrifier->emogrify();

        $mergedHtml = str_replace('%5B', '[', $mergedHtml);
        $mergedHtml = str_replace('%5D', ']', $mergedHtml);

        $this->html = $mergedHtml;
    }

    public function getConfig()
    {
        foreach (config('enews.enewsletters') as $config) {
            if ($config['key'] == $this->template) {
                return $config;
            }
        }

        return [];
    }

    public function getCampaignOption($key)
    {
        $config = $this->getConfig();

        $campaignOptions = $config['campaign_options'];

        return isset($campaignOptions[$key]) ? $campaignOptions[$key] : null;
    }

    public function getOnlyAdestraCampaignOptions()
    {
        $config = $this->getConfig();

        $campaignOptions = collect($config['campaign_options'])
            ->except('name_prefix', 'project_id', 'filter_id')
            ->toArray();

        $campaignOptions['subject_line'] = $this->subject;

        return $campaignOptions;
    }

    public static function getPublishArticleClass()
    {
        return config('enews.publish_namespace', '\\App\\Models\\Publish\\') . 'Article';
    }
}
