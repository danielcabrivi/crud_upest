<?php
namespace Foundation\Html;

use Container;

class Assets
{
    protected $url;
    protected $urlRoot;

    public function __construct()
    {
        $this->url = public_path();
        $this->urlRoot = public_root();
//        $this->url = Container::get('app.url');
    }

    public function img($file)
    {
        return sprintf('%s/assets/%s/%s', $this->url, 'img', $file);
    }

    public function video($file)
    {
        return sprintf('%s/assets/%s/%s', $this->url, 'video', $file);
    }

    public function pdf($file)
    {
        return sprintf('%s/assets/%s/%s', $this->url, 'pdf', $file);
    }

    public function js($file)
    {
        return sprintf('%s/assets/%s/%s', $this->url, 'js', $file);
    }

    public function css($file)
    {
        return  sprintf('%s/assets/%s/%s', $this->url, 'css', $file);
    }

    //pasta vendors está na raiz /public
    public function vendors($file)
    {
        return  sprintf('%s/%s/%s', $this->urlRoot, 'vendors', $file);
    }
}
