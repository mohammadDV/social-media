<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Tag;

class SitemapController extends Controller
{
    public function generateSitemap()
    {

        // Create the XML sitemap structure
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Add the home page
        $sitemap .= '<url>';
        $sitemap .= '<loc>https://varzeshpod.com</loc>';
        $sitemap .= '<priority>1.0</priority>';
        $sitemap .= '</url>';

        // Fetch posts from the database (adjust the query based on your schema)
        $posts = Post::query()
            ->where('status', 1)
            ->whereHas('categories', function ($query) {
                $query->whereIn('id', [5])
                    ->orwhereIn('id', [1]);
            })
            ->orderBy('id', 'DESC')
            ->get();
        // Add each post URL
        foreach ($posts as $post) {
            $sitemap .= '<url>';
            $sitemap .= '<loc>' . 'https://varzeshpod.com/news/' .$post->id . '/' . $post->slug . '</loc>';
            $sitemap .= '<priority>0.9</priority>';
            $sitemap .= '</url>';
        }

        // Fetch categories
        // $categories = Category::query()
        //     ->where('status', 1)
        //     ->whereHas('posts')
        //     ->get();
        // // Add each category URL
        // foreach ($categories as $category) {
        //     $sitemap .= '<url>';
        //     $sitemap .= '<loc>' . 'https://varzeshpod.com/category/' .$category->id . '/' . $category->slug . '</loc>';
        //     $sitemap .= '<priority>0.8</priority>';
        //     $sitemap .= '</url>';
        // }

        // Fetch categories
        $tags = Tag::query()
            ->withCount('posts')
            ->orderby('posts_count', 'DESC')
            ->limit(20)
            ->get();
        // Add each category URL
        foreach ($tags as $tag) {
            $sitemap .= '<url>';
            $sitemap .= '<loc>' . 'https://varzeshpod.com/tag/' .$tag->id . '/' . $tag->title . '</loc>';
            $sitemap .= '<priority>0.8</priority>';
            $sitemap .= '</url>';
        }

        // Fetch posts from the database (adjust the query based on your schema)
        $posts = Post::query()
            ->where('status', 1)
            ->orderBy('id', 'DESC')
            ->whereDoesntHave('categories', function ($query) {
                $query->whereIn('id', [5])
                    ->orwhereIn('id', [1]);
            })
            ->limit(2)
            ->get();
        // Add each post URL
        foreach ($posts as $post) {
            $sitemap .= '<url>';
            $sitemap .= '<loc>' . 'https://varzeshpod.com/news/' .$post->id . '/' . $post->slug . '</loc>';
            $sitemap .= '<priority>0.7</priority>';
            $sitemap .= '</url>';
        }


        $pages = Page::query()
            ->where('status', 1)
            ->orderBy('id', 'DESC')
            ->get();
        // Add each post URL
        foreach ($pages as $page) {
            $sitemap .= '<url>';
            $sitemap .= '<loc>' . 'https://varzeshpod.com/page/' . $page->slug . '</loc>';
            $sitemap .= '<priority>0.6</priority>';
            $sitemap .= '</url>';
        }

        $sitemap .= '</urlset>';

        // Set the response content type to XML
        return response($sitemap, 200)
            ->header('Content-Type', 'application/xml');
    }
}
