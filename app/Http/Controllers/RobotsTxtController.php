<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;

class RobotsTxtController extends Controller
{
    /**
     * Display the robots.txt content
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = AppSetting::first();

        $content = '';

        if ($settings && !$settings->use_default_robots_txt && !empty($settings->robots_txt_content)) {
            // Use custom robots.txt content
            $content = $settings->robots_txt_content;
        } else {
            // Use default robots.txt content
            $content = $this->getDefaultRobotsTxt();
        }

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    /**
     * Get default robots.txt content
     *
     * @return string
     */
    private function getDefaultRobotsTxt()
    {
        $settings = AppSetting::first();
        $frontendUrl = $settings ? $settings->frontend_url : config('app.url');

        return "User-agent: *\n" .
               "Allow: /\n" .
               "\n" .
               "Disallow: /admin/\n" .
               "Disallow: /storage/private/\n" .
               "Disallow: /nova/\n" .
               "Disallow: /horizon/\n" .
               "Disallow: /telescope/\n" .
               "\n" .
               "Sitemap: {$frontendUrl}/sitemap.xml\n";
    }
}
