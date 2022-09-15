<?php
 
namespace App\View\Composers;
use App\Models\Site;
 
use Illuminate\View\View;


class SiteComposer
{

 
 
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('site', Site::name());
    }
}