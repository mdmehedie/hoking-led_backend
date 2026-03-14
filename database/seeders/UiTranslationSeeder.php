<?php

namespace Database\Seeders;

use App\Models\UiTranslation;
use Illuminate\Database\Seeder;

class UiTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $translations = [
            // English translations
            [
                'key' => 'Admin Panel',
                'locale' => 'en',
                'value' => 'Admin Panel',
            ],
            [
                'key' => 'Content Management',
                'locale' => 'en',
                'value' => 'Content Management',
            ],
            [
                'key' => 'Product Management',
                'locale' => 'en',
                'value' => 'Product Management',
            ],
            [
                'key' => 'Marketing',
                'locale' => 'en',
                'value' => 'Marketing',
            ],
            [
                'key' => 'Settings',
                'locale' => 'en',
                'value' => 'Settings',
            ],
            [
                'key' => 'User Management',
                'locale' => 'en',
                'value' => 'User Management',
            ],
            [
                'key' => 'Users',
                'locale' => 'en',
                'value' => 'Users',
            ],
            [
                'key' => 'Authors',
                'locale' => 'en',
                'value' => 'Authors',
            ],
            [
                'key' => 'Blogs',
                'locale' => 'en',
                'value' => 'Blogs',
            ],
            [
                'key' => 'Categories',
                'locale' => 'en',
                'value' => 'Categories',
            ],
            [
                'key' => 'Pages',
                'locale' => 'en',
                'value' => 'Pages',
            ],
            [
                'key' => 'News',
                'locale' => 'en',
                'value' => 'News',
            ],
            [
                'key' => 'Case Studies',
                'locale' => 'en',
                'value' => 'Case Studies',
            ],
            [
                'key' => 'Testimonials',
                'locale' => 'en',
                'value' => 'Testimonials',
            ],
            [
                'key' => 'Sliders',
                'locale' => 'en',
                'value' => 'Sliders',
            ],
            [
                'key' => 'Certifications & Awards',
                'locale' => 'en',
                'value' => 'Certifications & Awards',
            ],
            [
                'key' => 'App Settings',
                'locale' => 'en',
                'value' => 'App Settings',
            ],
            [
                'key' => 'Translations',
                'locale' => 'en',
                'value' => 'Translations',
            ],
            [
                'key' => 'Languages',
                'locale' => 'en',
                'value' => 'Languages',
            ],
            [
                'key' => 'Roles',
                'locale' => 'en',
                'value' => 'Roles',
            ],
            [
                'key' => 'Role Information',
                'locale' => 'en',
                'value' => 'Role Information',
            ],
            [
                'key' => 'Role Name',
                'locale' => 'en',
                'value' => 'Role Name',
            ],
            [
                'key' => 'Permissions assigned to this role',
                'locale' => 'en',
                'value' => 'Permissions assigned to this role',
            ],
            [
                'key' => 'Created At',
                'locale' => 'en',
                'value' => 'Created At',
            ],
            [
                'key' => 'Updated At',
                'locale' => 'en',
                'value' => 'Updated At',
            ],
            [
                'key' => 'Delete Role',
                'locale' => 'en',
                'value' => 'Delete Role',
            ],
            [
                'key' => 'Are you sure you want to delete this role? Users assigned to this role will lose their permissions.',
                'locale' => 'en',
                'value' => 'Are you sure you want to delete this role? Users assigned to this role will lose their permissions.',
            ],
            [
                'key' => 'Yes, delete it',
                'locale' => 'en',
                'value' => 'Yes, delete it',
            ],
            [
                'key' => 'Logos',
                'locale' => 'en',
                'value' => 'Logos',
            ],
            [
                'key' => 'Light Logo',
                'locale' => 'en',
                'value' => 'Light Logo',
            ],
            [
                'key' => 'Dark Logo',
                'locale' => 'en',
                'value' => 'Dark Logo',
            ],
            [
                'key' => 'Favicon',
                'locale' => 'en',
                'value' => 'Favicon',
            ],
            [
                'key' => 'Brand Colors',
                'locale' => 'en',
                'value' => 'Brand Colors',
            ],
            [
                'key' => 'Primary Color',
                'locale' => 'en',
                'value' => 'Primary Color',
            ],
            [
                'key' => 'Secondary Color',
                'locale' => 'en',
                'value' => 'Secondary Color',
            ],
            [
                'key' => 'Accent Color',
                'locale' => 'en',
                'value' => 'Accent Color',
            ],
            [
                'key' => 'Typography',
                'locale' => 'en',
                'value' => 'Typography',
            ],
            [
                'key' => 'Font Family',
                'locale' => 'en',
                'value' => 'Font Family',
            ],
            [
                'key' => 'Base Font Size',
                'locale' => 'en',
                'value' => 'Base Font Size',
            ],
            [
                'key' => 'Organization',
                'locale' => 'en',
                'value' => 'Organization',
            ],
            [
                'key' => 'Company Title',
                'locale' => 'en',
                'value' => 'Company Title',
            ],
            [
                'key' => 'Company name',
                'locale' => 'en',
                'value' => 'Company name',
            ],
            [
                'key' => 'About information',
                'locale' => 'en',
                'value' => 'About information',
            ],
            [
                'key' => 'The URL of your frontend website. Used for generating share links in social media posts.',
                'locale' => 'en',
                'value' => 'The URL of your frontend website. Used for generating share links in social media posts.',
            ],
            [
                'key' => 'Contact email(s)',
                'locale' => 'en',
                'value' => 'Contact email(s)',
            ],
            [
                'key' => 'Email',
                'locale' => 'en',
                'value' => 'Email',
            ],
            [
                'key' => 'Contact phone number(s)',
                'locale' => 'en',
                'value' => 'Contact phone number(s)',
            ],
            [
                'key' => 'Phone Number',
                'locale' => 'en',
                'value' => 'Phone Number',
            ],
            [
                'key' => 'Office addresses',
                'locale' => 'en',
                'value' => 'Office addresses',
            ],
            [
                'key' => 'Label',
                'locale' => 'en',
                'value' => 'Label',
            ],
            [
                'key' => 'Street',
                'locale' => 'en',
                'value' => 'Street',
            ],
            [
                'key' => 'City',
                'locale' => 'en',
                'value' => 'City',
            ],
            [
                'key' => 'Country',
                'locale' => 'en',
                'value' => 'Country',
            ],
            [
                'key' => 'Map link (Google Maps URL)',
                'locale' => 'en',
                'value' => 'Map link (Google Maps URL)',
            ],
            [
                'key' => 'Social media profile links',
                'locale' => 'en',
                'value' => 'Social media profile links',
            ],
            [
                'key' => 'Platform',
                'locale' => 'en',
                'value' => 'Platform',
            ],
            [
                'key' => 'Toastr Settings',
                'locale' => 'en',
                'value' => 'Toastr Settings',
            ],
            [
                'key' => 'Enable Toastr Notifications',
                'locale' => 'en',
                'value' => 'Enable Toastr Notifications',
            ],
            [
                'key' => 'Position',
                'locale' => 'en',
                'value' => 'Position',
            ],
            [
                'key' => 'Top Left',
                'locale' => 'en',
                'value' => 'Top Left',
            ],
            [
                'key' => 'Top Right',
                'locale' => 'en',
                'value' => 'Top Right',
            ],
            [
                'key' => 'Bottom Left',
                'locale' => 'en',
                'value' => 'Bottom Left',
            ],
            [
                'key' => 'Bottom Right',
                'locale' => 'en',
                'value' => 'Bottom Right',
            ],
            [
                'key' => 'Duration (ms)',
                'locale' => 'en',
                'value' => 'Duration (ms)',
            ],
            [
                'key' => 'Show Method',
                'locale' => 'en',
                'value' => 'Show Method',
            ],
            [
                'key' => 'Fade In',
                'locale' => 'en',
                'value' => 'Fade In',
            ],
            [
                'key' => 'Slide Down',
                'locale' => 'en',
                'value' => 'Slide Down',
            ],
            [
                'key' => 'Hide Method',
                'locale' => 'en',
                'value' => 'Hide Method',
            ],
            [
                'key' => 'Fade Out',
                'locale' => 'en',
                'value' => 'Fade Out',
            ],
            [
                'key' => 'Slide Up',
                'locale' => 'en',
                'value' => 'Slide Up',
            ],
            [
                'key' => 'SEO Settings',
                'locale' => 'en',
                'value' => 'SEO Settings',
            ],
            [
                'key' => 'Enable Sitemap Generation',
                'locale' => 'en',
                'value' => 'Enable Sitemap Generation',
            ],
            [
                'key' => 'Robots.txt Settings',
                'locale' => 'en',
                'value' => 'Robots.txt Settings',
            ],
            [
                'key' => 'Manage robots.txt content for search engine crawlers',
                'locale' => 'en',
                'value' => 'Manage robots.txt content for search engine crawlers',
            ],
            [
                'key' => 'Use Default Robots.txt',
                'locale' => 'en',
                'value' => 'Use Default Robots.txt',
            ],
            [
                'key' => 'When enabled, uses a default robots.txt that allows all crawlers. When disabled, uses custom content below.',
                'locale' => 'en',
                'value' => 'When enabled, uses a default robots.txt that allows all crawlers. When disabled, uses custom content below.',
            ],
            [
                'key' => 'Custom Robots.txt Content',
                'locale' => 'en',
                'value' => 'Custom Robots.txt Content',
            ],
            [
                'key' => 'Enter custom robots.txt content. This will be used when "Use Default Robots.txt" is disabled. Make sure to follow proper robots.txt syntax.',
                'locale' => 'en',
                'value' => 'Enter custom robots.txt content. This will be used when "Use Default Robots.txt" is disabled. Make sure to follow proper robots.txt syntax.',
            ],
            [
                'key' => 'Review',
                'locale' => 'en',
                'value' => 'Review',
            ],
            [
                'key' => 'Excerpt',
                'locale' => 'en',
                'value' => 'Excerpt',
            ],
            [
                'key' => 'Content',
                'locale' => 'en',
                'value' => 'Content',
            ],
            [
                'key' => 'Forms',
                'locale' => 'en',
                'value' => 'Forms',
            ],
            [
                'key' => 'Form Name',
                'locale' => 'en',
                'value' => 'Form Name',
            ],
            [
                'key' => 'Form Fields',
                'locale' => 'en',
                'value' => 'Form Fields',
            ],
            [
                'key' => 'Text Input',
                'locale' => 'en',
                'value' => 'Text Input',
            ],
            [
                'key' => 'Email Input',
                'locale' => 'en',
                'value' => 'Email Input',
            ],
            [
                'key' => 'Textarea',
                'locale' => 'en',
                'value' => 'Textarea',
            ],
            [
                'key' => 'Select Dropdown',
                'locale' => 'en',
                'value' => 'Select Dropdown',
            ],
            [
                'key' => 'Options',
                'locale' => 'en',
                'value' => 'Options',
            ],
            [
                'key' => 'Success Message',
                'locale' => 'en',
                'value' => 'Success Message',
            ],
            [
                'key' => 'Enable Email Notifications',
                'locale' => 'en',
                'value' => 'Enable Email Notifications',
            ],
            [
                'key' => 'Notification Emails',
                'locale' => 'en',
                'value' => 'Notification Emails',
            ],
            [
                'key' => 'Enter email addresses',
                'locale' => 'en',
                'value' => 'Enter email addresses',
            ],
            [
                'key' => 'Store Leads',
                'locale' => 'en',
                'value' => 'Store Leads',
            ],
            [
                'key' => 'Leads',
                'locale' => 'en',
                'value' => 'Leads',
            ],
            [
                'key' => 'ID',
                'locale' => 'en',
                'value' => 'ID',
            ],
            [
                'key' => 'Data',
                'locale' => 'en',
                'value' => 'Data',
            ],
            [
                'key' => 'Created Date',
                'locale' => 'en',
                'value' => 'Created Date',
            ],
            [
                'key' => 'From',
                'locale' => 'en',
                'value' => 'From',
            ],
            [
                'key' => 'Until',
                'locale' => 'en',
                'value' => 'Until',
            ],
            [
                'key' => 'View',
                'locale' => 'en',
                'value' => 'View',
            ],
            [
                'key' => 'No',
                'locale' => 'en',
                'value' => 'No',
            ],
            [
                'key' => 'Yes',
                'locale' => 'en',
                'value' => 'Yes',
            ],
            [
                'key' => 'Loading...',
                'locale' => 'en',
                'value' => 'Loading...',
            ],
            [
                'key' => 'Error',
                'locale' => 'en',
                'value' => 'Error',
            ],
            [
                'key' => 'Success',
                'locale' => 'en',
                'value' => 'Success',
            ],
            [
                'key' => 'No data available',
                'locale' => 'en',
                'value' => 'No data available',
            ],
            [
                'key' => 'Not found',
                'locale' => 'en',
                'value' => 'Not found',
            ],
            [
                'key' => 'Server error',
                'locale' => 'en',
                'value' => 'Server error',
            ],
            [
                'key' => 'Please try again',
                'locale' => 'en',
                'value' => 'Please try again',
            ],
            [
                'key' => 'Close',
                'locale' => 'en',
                'value' => 'Close',
            ],
            [
                'key' => 'Cancel',
                'locale' => 'en',
                'value' => 'Cancel',
            ],
            [
                'key' => 'Confirm',
                'locale' => 'en',
                'value' => 'Confirm',
            ],
            [
                'key' => 'OK',
                'locale' => 'en',
                'value' => 'OK',
            ],
            [
                'key' => 'Save',
                'locale' => 'en',
                'value' => 'Save',
            ],
            [
                'key' => 'Search',
                'locale' => 'en',
                'value' => 'Search',
            ],
            [
                'key' => 'Filter',
                'locale' => 'en',
                'value' => 'Filter',
            ],
            [
                'key' => 'Sort',
                'locale' => 'en',
                'value' => 'Sort',
            ],
            [
                'key' => 'Page',
                'locale' => 'en',
                'value' => 'Page',
            ],
            [
                'key' => 'of',
                'locale' => 'en',
                'value' => 'of',
            ],
            [
                'key' => 'Items per page',
                'locale' => 'en',
                'value' => 'Items per page',
            ],
            [
                'key' => 'Showing',
                'locale' => 'en',
                'value' => 'Showing',
            ],
            [
                'key' => 'to',
                'locale' => 'en',
                'value' => 'to',
            ],
            [
                'key' => 'of total',
                'locale' => 'en',
                'value' => 'of total',
            ],
            [
                'key' => 'Previous',
                'locale' => 'en',
                'value' => 'Previous',
            ],
            [
                'key' => 'Next',
                'locale' => 'en',
                'value' => 'Next',
            ],
            [
                'key' => 'First',
                'locale' => 'en',
                'value' => 'First',
            ],
            [
                'key' => 'Last',
                'locale' => 'en',
                'value' => 'Last',
            ],
            [
                'key' => 'Locales retrieved successfully',
                'locale' => 'en',
                'value' => 'Locales retrieved successfully',
            ],
            [
                'key' => 'Forms retrieved successfully',
                'locale' => 'en',
                'value' => 'Forms retrieved successfully',
            ],
            [
                'key' => 'Form submitted successfully',
                'locale' => 'en',
                'value' => 'Form submitted successfully',
            ],
            [
                'key' => 'Products retrieved successfully',
                'locale' => 'en',
                'value' => 'Products retrieved successfully',
            ],
            [
                'key' => 'Product retrieved successfully',
                'locale' => 'en',
                'value' => 'Product retrieved successfully',
            ],
            [
                'key' => 'Product not found',
                'locale' => 'en',
                'value' => 'Product not found',
            ],
            [
                'key' => 'Blogs retrieved successfully',
                'locale' => 'en',
                'value' => 'Blogs retrieved successfully',
            ],
            [
                'key' => 'Blog retrieved successfully',
                'locale' => 'en',
                'value' => 'Blog retrieved successfully',
            ],
            [
                'key' => 'Blog not found',
                'locale' => 'en',
                'value' => 'Blog not found',
            ],
            [
                'key' => 'Case studies retrieved successfully',
                'locale' => 'en',
                'value' => 'Case studies retrieved successfully',
            ],
            [
                'key' => 'Case study retrieved successfully',
                'locale' => 'en',
                'value' => 'Case study retrieved successfully',
            ],
            [
                'key' => 'Case study not found',
                'locale' => 'en',
                'value' => 'Case study not found',
            ],
            [
                'key' => 'Categories retrieved successfully',
                'locale' => 'en',
                'value' => 'Categories retrieved successfully',
            ],
            [
                'key' => 'Certifications retrieved successfully',
                'locale' => 'en',
                'value' => 'Certifications retrieved successfully',
            ],
            [
                'key' => 'Certification retrieved successfully',
                'locale' => 'en',
                'value' => 'Certification retrieved successfully',
            ],
            [
                'key' => 'Certification not found',
                'locale' => 'en',
                'value' => 'Certification not found',
            ],
            [
                'key' => 'Featured products retrieved successfully',
                'locale' => 'en',
                'value' => 'Featured products retrieved successfully',
            ],
            [
                'key' => 'News retrieved successfully',
                'locale' => 'en',
                'value' => 'News retrieved successfully',
            ],
            [
                'key' => 'News not found',
                'locale' => 'en',
                'value' => 'News not found',
            ],
            [
                'key' => 'Pages retrieved successfully',
                'locale' => 'en',
                'value' => 'Pages retrieved successfully',
            ],
            [
                'key' => 'Page retrieved successfully',
                'locale' => 'en',
                'value' => 'Page retrieved successfully',
            ],
            [
                'key' => 'Page not found',
                'locale' => 'en',
                'value' => 'Page not found',
            ],
            [
                'key' => 'Sliders retrieved successfully',
                'locale' => 'en',
                'value' => 'Sliders retrieved successfully',
            ],
            [
                'key' => 'Testimonials retrieved successfully',
                'locale' => 'en',
                'value' => 'Testimonials retrieved successfully',
            ],
            [
                'key' => 'App settings retrieved successfully',
                'locale' => 'en',
                'value' => 'App settings retrieved successfully',
            ],
            [
                'key' => 'App setting field retrieved successfully',
                'locale' => 'en',
                'value' => 'App setting field retrieved successfully',
            ],
            [
                'key' => 'App setting not found',
                'locale' => 'en',
                'value' => 'App setting not found',
            ],
            [
                'key' => 'Field not found',
                'locale' => 'en',
                'value' => 'Field not found',
            ],
            [
                'key' => 'Language',
                'locale' => 'en',
                'value' => 'Language',
            ],

            // Product Resource translations
            [
                'key' => 'Products',
                'locale' => 'en',
                'value' => 'Products',
            ],
            [
                'key' => 'General',
                'locale' => 'en',
                'value' => 'General',
            ],
            [
                'key' => 'Slug',
                'locale' => 'en',
                'value' => 'Slug',
            ],
            [
                'key' => 'Category',
                'locale' => 'en',
                'value' => 'Category',
            ],
            [
                'key' => 'Status',
                'locale' => 'en',
                'value' => 'Status',
            ],
            [
                'key' => 'Draft',
                'locale' => 'en',
                'value' => 'Draft',
            ],
            [
                'key' => 'Published',
                'locale' => 'en',
                'value' => 'Published',
            ],
            [
                'key' => 'Archived',
                'locale' => 'en',
                'value' => 'Archived',
            ],
            [
                'key' => 'Featured Product',
                'locale' => 'en',
                'value' => 'Featured Product',
            ],
            [
                'key' => 'Translations',
                'locale' => 'en',
                'value' => 'Translations',
            ],
            [
                'key' => 'Title',
                'locale' => 'en',
                'value' => 'Title',
            ],
            [
                'key' => 'Short Description',
                'locale' => 'en',
                'value' => 'Short Description',
            ],
            [
                'key' => 'Detailed Description',
                'locale' => 'en',
                'value' => 'Detailed Description',
            ],
            [
                'key' => 'Media',
                'locale' => 'en',
                'value' => 'Media',
            ],
            [
                'key' => 'Main Image',
                'locale' => 'en',
                'value' => 'Main Image',
            ],
            [
                'key' => 'Gallery',
                'locale' => 'en',
                'value' => 'Gallery',
            ],
            [
                'key' => 'Video Embeds',
                'locale' => 'en',
                'value' => 'Video Embeds',
            ],
            [
                'key' => 'Type',
                'locale' => 'en',
                'value' => 'Type',
            ],
            [
                'key' => 'Embed URL',
                'locale' => 'en',
                'value' => 'Embed URL',
            ],
            [
                'key' => 'Self-hosted File',
                'locale' => 'en',
                'value' => 'Self-hosted File',
            ],
            [
                'key' => 'URL',
                'locale' => 'en',
                'value' => 'URL',
            ],
            [
                'key' => 'Video File',
                'locale' => 'en',
                'value' => 'Video File',
            ],
            [
                'key' => 'Downloads',
                'locale' => 'en',
                'value' => 'Downloads',
            ],
            [
                'key' => 'Technical Specs',
                'locale' => 'en',
                'value' => 'Technical Specs',
            ],
            [
                'key' => 'Technical Specifications',
                'locale' => 'en',
                'value' => 'Technical Specifications',
            ],
            [
                'key' => 'Key',
                'locale' => 'en',
                'value' => 'Key',
            ],
            [
                'key' => 'Value',
                'locale' => 'en',
                'value' => 'Value',
            ],
            [
                'key' => 'Tags',
                'locale' => 'en',
                'value' => 'Tags',
            ],
            [
                'key' => 'Tag',
                'locale' => 'en',
                'value' => 'Tag',
            ],
            [
                'key' => 'Related Products',
                'locale' => 'en',
                'value' => 'Related Products',
            ],
            [
                'key' => 'SEO',
                'locale' => 'en',
                'value' => 'SEO',
            ],
            [
                'key' => 'Meta Title',
                'locale' => 'en',
                'value' => 'Meta Title',
            ],
            [
                'key' => 'Meta Description',
                'locale' => 'en',
                'value' => 'Meta Description',
            ],
            [
                'key' => 'Meta Keywords',
                'locale' => 'en',
                'value' => 'Meta Keywords',
            ],
            [
                'key' => 'Canonical URL',
                'locale' => 'en',
                'value' => 'Canonical URL',
            ],
            [
                'key' => 'Image',
                'locale' => 'en',
                'value' => 'Image',
            ],
            [
                'key' => 'Status updated',
                'locale' => 'en',
                'value' => 'Status updated',
            ],
            [
                'key' => 'Product status has been changed to',
                'locale' => 'en',
                'value' => 'Product status has been changed to',
            ],
            [
                'key' => 'Share',
                'locale' => 'en',
                'value' => 'Share',
            ],
            [
                'key' => 'URL Preview',
                'locale' => 'en',
                'value' => 'URL Preview',
            ],
            [
                'key' => 'This is the URL that will be included in your social media posts',
                'locale' => 'en',
                'value' => 'This is the URL that will be included in your social media posts',
            ],
            [
                'key' => 'Content URL',
                'locale' => 'en',
                'value' => 'Content URL',
            ],
            [
                'key' => 'This URL will be shared on the selected social media platforms',
                'locale' => 'en',
                'value' => 'This URL will be shared on the selected social media platforms',
            ],
            [
                'key' => 'Share to Platforms',
                'locale' => 'en',
                'value' => 'Share to Platforms',
            ],
            [
                'key' => 'Facebook',
                'locale' => 'en',
                'value' => 'Facebook',
            ],
            [
                'key' => 'Twitter (X)',
                'locale' => 'en',
                'value' => 'Twitter (X)',
            ],
            [
                'key' => 'LinkedIn',
                'locale' => 'en',
                'value' => 'LinkedIn',
            ],
            [
                'key' => 'Select which social media platforms to share this product on',
                'locale' => 'en',
                'value' => 'Select which social media platforms to share this product on',
            ],
            [
                'key' => 'Product shared successfully!',
                'locale' => 'en',
                'value' => 'Product shared successfully!',
            ],
            [
                'key' => 'The product has been queued for sharing to selected social media platforms.',
                'locale' => 'en',
                'value' => 'The product has been queued for sharing to selected social media platforms.',
            ],
            [
                'key' => 'Share Product',
                'locale' => 'en',
                'value' => 'Share Product',
            ],
            [
                'key' => 'Share Now',
                'locale' => 'en',
                'value' => 'Share Now',
            ],
            [
                'key' => 'Edit',
                'locale' => 'en',
                'value' => 'Edit',
            ],
            [
                'key' => 'Delete',
                'locale' => 'en',
                'value' => 'Delete',
            ],
            [
                'key' => 'Delete Selected',
                'locale' => 'en',
                'value' => 'Delete Selected',
            ],
            [
                'key' => 'Deleted',
                'locale' => 'en',
                'value' => 'Deleted',
            ],
            [
                'key' => 'items deleted successfully.',
                'locale' => 'en',
                'value' => 'items deleted successfully.',
            ],
            [
                'key' => 'Change Status',
                'locale' => 'en',
                'value' => 'Change Status',
            ],
            [
                'key' => 'Status Updated',
                'locale' => 'en',
                'value' => 'Status Updated',
            ],
            [
                'key' => 'Selected items have been updated to',
                'locale' => 'en',
                'value' => 'Selected items have been updated to',
            ],

            // Bangla translations
            [
                'key' => 'Admin Panel',
                'locale' => 'bd',
                'value' => 'অ্যাডমিন প্যানেল',
            ],
            [
                'key' => 'Content Management',
                'locale' => 'bd',
                'value' => 'কন্টেন্ট ব্যবস্থাপনা',
            ],
            [
                'key' => 'Product Management',
                'locale' => 'bd',
                'value' => 'পণ্য ব্যবস্থাপনা',
            ],
            [
                'key' => 'Marketing',
                'locale' => 'bd',
                'value' => 'মার্কেটিং',
            ],
            [
                'key' => 'Settings',
                'locale' => 'bd',
                'value' => 'সেটিংস',
            ],
            [
                'key' => 'User Management',
                'locale' => 'bd',
                'value' => 'ব্যবহারকারী ব্যবস্থাপনা',
            ],
            [
                'key' => 'Users',
                'locale' => 'bd',
                'value' => 'ব্যবহারকারীরা',
            ],
            [
                'key' => 'Authors',
                'locale' => 'bd',
                'value' => 'লেখকগণ',
            ],
            [
                'key' => 'Blogs',
                'locale' => 'bd',
                'value' => 'ব্লগ',
            ],
            [
                'key' => 'Categories',
                'locale' => 'bd',
                'value' => 'বিভাগসমূহ',
            ],
            [
                'key' => 'Pages',
                'locale' => 'bd',
                'value' => 'পৃষ্ঠাসমূহ',
            ],
            [
                'key' => 'News',
                'locale' => 'bd',
                'value' => 'খবর',
            ],
            [
                'key' => 'Case Studies',
                'locale' => 'bd',
                'value' => 'কেস স্টাডি',
            ],
            [
                'key' => 'Testimonials',
                'locale' => 'bd',
                'value' => 'সাক্ষ্যদাতাগণ',
            ],
            [
                'key' => 'Sliders',
                'locale' => 'bd',
                'value' => 'স্লাইডার',
            ],
            [
                'key' => 'Certifications & Awards',
                'locale' => 'bd',
                'value' => 'সার্টিফিকেশন ও পুরস্কার',
            ],
            [
                'key' => 'App Settings',
                'locale' => 'bd',
                'value' => 'অ্যাপ সেটিংস',
            ],
            [
                'key' => 'Translations',
                'locale' => 'bd',
                'value' => 'অনুবাদ',
            ],
            [
                'key' => 'Languages',
                'locale' => 'bd',
                'value' => 'ভাষাসমূহ',
            ],
            [
                'key' => 'Roles',
                'locale' => 'bd',
                'value' => 'ভূমিকাসমূহ',
            ],
            [
                'key' => 'Role Information',
                'locale' => 'bd',
                'value' => 'ভূমিকা তথ্য',
            ],
            [
                'key' => 'Role Name',
                'locale' => 'bd',
                'value' => 'ভূমিকার নাম',
            ],
            [
                'key' => 'Permissions assigned to this role',
                'locale' => 'bd',
                'value' => 'এই ভূমিকায় অর্পিত অনুমতিসমূহ',
            ],
            [
                'key' => 'Created At',
                'locale' => 'bd',
                'value' => 'তৈরি করা হয়েছে',
            ],
            [
                'key' => 'Updated At',
                'locale' => 'bd',
                'value' => 'আপডেট করা হয়েছে',
            ],
            [
                'key' => 'Delete Role',
                'locale' => 'bd',
                'value' => 'ভূমিকা মুছুন',
            ],
            [
                'key' => 'Are you sure you want to delete this role? Users assigned to this role will lose their permissions.',
                'locale' => 'bd',
                'value' => 'আপনি কি নিশ্চিত যে আপনি এই ভূমিকা মুছতে চান? এই ভূমিকায় নির্ধারিত ব্যবহারকারীরা তাদের অনুমতি হারাবে।',
            ],
            [
                'key' => 'Yes, delete it',
                'locale' => 'bd',
                'value' => 'হ্যাঁ, এটি মুছুন',
            ],
            [
                'key' => 'Logos',
                'locale' => 'bd',
                'value' => 'লোগো',
            ],
            [
                'key' => 'Light Logo',
                'locale' => 'bd',
                'value' => 'লাইট লোগো',
            ],
            [
                'key' => 'Dark Logo',
                'locale' => 'bd',
                'value' => 'ডার্ক লোগো',
            ],
            [
                'key' => 'Favicon',
                'locale' => 'bd',
                'value' => 'ফেভিকন',
            ],
            [
                'key' => 'Brand Colors',
                'locale' => 'bd',
                'value' => 'ব্র্যান্ড রং',
            ],
            [
                'key' => 'Primary Color',
                'locale' => 'bd',
                'value' => 'প্রাথমিক রং',
            ],
            [
                'key' => 'Secondary Color',
                'locale' => 'bd',
                'value' => 'গৌণ রং',
            ],
            [
                'key' => 'Accent Color',
                'locale' => 'bd',
                'value' => 'এক্সেন্ট রং',
            ],
            [
                'key' => 'Typography',
                'locale' => 'bd',
                'value' => 'টাইপোগ্রাফি',
            ],
            [
                'key' => 'Font Family',
                'locale' => 'bd',
                'value' => 'ফন্ট পরিবার',
            ],
            [
                'key' => 'Base Font Size',
                'locale' => 'bd',
                'value' => 'বেস ফন্ট সাইজ',
            ],
            [
                'key' => 'Organization',
                'locale' => 'bd',
                'value' => 'সংগঠন',
            ],
            [
                'key' => 'Company Title',
                'locale' => 'bd',
                'value' => 'কোম্পানির শিরোনাম',
            ],
            [
                'key' => 'Company name',
                'locale' => 'bd',
                'value' => 'কোম্পানির নাম',
            ],
            [
                'key' => 'About information',
                'locale' => 'bd',
                'value' => 'সম্পর্কে তথ্য',
            ],
            [
                'key' => 'The URL of your frontend website. Used for generating share links in social media posts.',
                'locale' => 'bd',
                'value' => 'আপনার ফ্রন্টএন্ড ওয়েবসাইটের URL। সোশ্যাল মিডিয়া পোস্টে শেয়ার লিঙ্ক তৈরির জন্য ব্যবহৃত হয়।',
            ],
            [
                'key' => 'Contact email(s)',
                'locale' => 'bd',
                'value' => 'যোগাযোগের ইমেইল (গুলি)',
            ],
            [
                'key' => 'Email',
                'locale' => 'bd',
                'value' => 'ইমেইল',
            ],
            [
                'key' => 'Contact phone number(s)',
                'locale' => 'bd',
                'value' => 'যোগাযোগের ফোন নম্বর (গুলি)',
            ],
            [
                'key' => 'Phone Number',
                'locale' => 'bd',
                'value' => 'ফোন নম্বর',
            ],
            [
                'key' => 'Office addresses',
                'locale' => 'bd',
                'value' => 'অফিসের ঠিকানা',
            ],
            [
                'key' => 'Label',
                'locale' => 'bd',
                'value' => 'লেবেল',
            ],
            [
                'key' => 'Street',
                'locale' => 'bd',
                'value' => 'রাস্তা',
            ],
            [
                'key' => 'City',
                'locale' => 'bd',
                'value' => 'শহর',
            ],
            [
                'key' => 'Country',
                'locale' => 'bd',
                'value' => 'দেশ',
            ],
            [
                'key' => 'Map link (Google Maps URL)',
                'locale' => 'bd',
                'value' => 'মানচিত্র লিঙ্ক (গুগল ম্যাপ URL)',
            ],
            [
                'key' => 'Social media profile links',
                'locale' => 'bd',
                'value' => 'সোশ্যাল মিডিয়া প্রোফাইল লিঙ্ক',
            ],
            [
                'key' => 'Platform',
                'locale' => 'bd',
                'value' => 'প্ল্যাটফর্ম',
            ],
            [
                'key' => 'Toastr Settings',
                'locale' => 'bd',
                'value' => 'Toastr সেটিংস',
            ],
            [
                'key' => 'Enable Toastr Notifications',
                'locale' => 'bd',
                'value' => 'Toastr নোটিফিকেশন সক্ষম করুন',
            ],
            [
                'key' => 'Position',
                'locale' => 'bd',
                'value' => 'অবস্থান',
            ],
            [
                'key' => 'Top Left',
                'locale' => 'bd',
                'value' => 'উপরে বামে',
            ],
            [
                'key' => 'Top Right',
                'locale' => 'bd',
                'value' => 'উপরে ডানে',
            ],
            [
                'key' => 'Bottom Left',
                'locale' => 'bd',
                'value' => 'নিচে বামে',
            ],
            [
                'key' => 'Bottom Right',
                'locale' => 'bd',
                'value' => 'নিচে ডানে',
            ],
            [
                'key' => 'Duration (ms)',
                'locale' => 'bd',
                'value' => 'স্থিতিকাল (মিলিসেকেন্ড)',
            ],
            [
                'key' => 'Show Method',
                'locale' => 'bd',
                'value' => 'প্রদর্শন পদ্ধতি',
            ],
            [
                'key' => 'Fade In',
                'locale' => 'bd',
                'value' => 'ফেড ইন',
            ],
            [
                'key' => 'Slide Down',
                'locale' => 'bd',
                'value' => 'নিচে স্লাইড করুন',
            ],
            [
                'key' => 'Hide Method',
                'locale' => 'bd',
                'value' => 'লুকানোর পদ্ধতি',
            ],
            [
                'key' => 'Fade Out',
                'locale' => 'bd',
                'value' => 'ফেড আউট',
            ],
            [
                'key' => 'Slide Up',
                'locale' => 'bd',
                'value' => 'উপরে স্লাইড করুন',
            ],
            [
                'key' => 'SEO Settings',
                'locale' => 'bd',
                'value' => 'SEO সেটিংস',
            ],
            [
                'key' => 'Enable Sitemap Generation',
                'locale' => 'bd',
                'value' => 'সাইটম্যাপ জেনারেশন সক্ষম করুন',
            ],
            [
                'key' => 'Robots.txt Settings',
                'locale' => 'bd',
                'value' => 'Robots.txt সেটিংস',
            ],
            [
                'key' => 'Manage robots.txt content for search engine crawlers',
                'locale' => 'bd',
                'value' => 'সার্চ ইঞ্জিন ক্রলারদের জন্য robots.txt কন্টেন্ট পরিচালনা করুন',
            ],
            [
                'key' => 'Use Default Robots.txt',
                'locale' => 'bd',
                'value' => 'ডিফল্ট Robots.txt ব্যবহার করুন',
            ],
            [
                'key' => 'When enabled, uses a default robots.txt that allows all crawlers. When disabled, uses custom content below.',
                'locale' => 'bd',
                'value' => 'সক্ষম করা হলে, সমস্ত ক্রলারকে অনুমতি দেয় এমন একটি ডিফল্ট robots.txt ব্যবহার করে। অক্ষম করা হলে, নিচের কাস্টম কন্টেন্ট ব্যবহার করে।',
            ],
            [
                'key' => 'Custom Robots.txt Content',
                'locale' => 'bd',
                'value' => 'কাস্টম Robots.txt কন্টেন্ট',
            ],
            [
                'key' => 'Enter custom robots.txt content. This will be used when "Use Default Robots.txt" is disabled. Make sure to follow proper robots.txt syntax.',
                'locale' => 'bd',
                'value' => 'কাস্টম robots.txt কন্টেন্ট লিখুন। "ডিফল্ট Robots.txt ব্যবহার করুন" অক্ষম থাকলে এটি ব্যবহৃত হবে। সঠিক robots.txt সিনট্যাক্স অনুসরণ করতে ভুলবেন না।',
            ],
            [
                'key' => 'Review',
                'locale' => 'bd',
                'value' => 'পর্যালোচনা',
            ],
            [
                'key' => 'Excerpt',
                'locale' => 'bd',
                'value' => 'উদ্ধৃতি',
            ],
            [
                'key' => 'Content',
                'locale' => 'bd',
                'value' => 'কন্টেন্ট',
            ],
            [
                'key' => 'Forms',
                'locale' => 'bd',
                'value' => 'ফর্মসমূহ',
            ],
            [
                'key' => 'Form Name',
                'locale' => 'bd',
                'value' => 'ফর্মের নাম',
            ],
            [
                'key' => 'Form Fields',
                'locale' => 'bd',
                'value' => 'ফর্ম ক্ষেত্রগুলি',
            ],
            [
                'key' => 'Text Input',
                'locale' => 'bd',
                'value' => 'টেক্সট ইনপুট',
            ],
            [
                'key' => 'Email Input',
                'locale' => 'bd',
                'value' => 'ইমেইল ইনপুট',
            ],
            [
                'key' => 'Textarea',
                'locale' => 'bd',
                'value' => 'টেক্সটএরিয়া',
            ],
            [
                'key' => 'Select Dropdown',
                'locale' => 'bd',
                'value' => 'ড্রপডাউন নির্বাচন',
            ],
            [
                'key' => 'Options',
                'locale' => 'bd',
                'value' => 'অপশনসমূহ',
            ],
            [
                'key' => 'Success Message',
                'locale' => 'bd',
                'value' => 'সফলতার বার্তা',
            ],
            [
                'key' => 'Enable Email Notifications',
                'locale' => 'bd',
                'value' => 'ইমেইল নোটিফিকেশন সক্ষম করুন',
            ],
            [
                'key' => 'Notification Emails',
                'locale' => 'bd',
                'value' => 'নোটিফিকেশন ইমেইল',
            ],
            [
                'key' => 'Enter email addresses',
                'locale' => 'bd',
                'value' => 'ইমেইল ঠিকানা লিখুন',
            ],
            [
                'key' => 'Store Leads',
                'locale' => 'bd',
                'value' => 'লিড সংরক্ষণ করুন',
            ],
            [
                'key' => 'Leads',
                'locale' => 'bd',
                'value' => 'লিডসমূহ',
            ],
            [
                'key' => 'ID',
                'locale' => 'bd',
                'value' => 'আইডি',
            ],
            [
                'key' => 'Data',
                'locale' => 'bd',
                'value' => 'তথ্য',
            ],
            [
                'key' => 'Created Date',
                'locale' => 'bd',
                'value' => 'তৈরির তারিখ',
            ],
            [
                'key' => 'From',
                'locale' => 'bd',
                'value' => 'থেকে',
            ],
            [
                'key' => 'Until',
                'locale' => 'bd',
                'value' => 'পর্যন্ত',
            ],
            [
                'key' => 'View',
                'locale' => 'bd',
                'value' => 'দেখুন',
            ],
            [
                'key' => 'No',
                'locale' => 'bd',
                'value' => 'না',
            ],
            [
                'key' => 'Yes',
                'locale' => 'bd',
                'value' => 'হ্যাঁ',
            ],
            [
                'key' => 'Loading...',
                'locale' => 'bd',
                'value' => 'লোড হচ্ছে...',
            ],
            [
                'key' => 'Error',
                'locale' => 'bd',
                'value' => 'ত্রুটি',
            ],
            [
                'key' => 'Success',
                'locale' => 'bd',
                'value' => 'সফলতা',
            ],
            [
                'key' => 'No data available',
                'locale' => 'bd',
                'value' => 'কোনো তথ্য নেই',
            ],
            [
                'key' => 'Not found',
                'locale' => 'bd',
                'value' => 'পাওয়া যায়',
            ],
            [
                'key' => 'Server error',
                'locale' => 'bd',
                'value' => 'সার্ভার ত্রুটি',
            ],
            [
                'key' => 'Please try again',
                'locale' => 'bd',
                'value' => 'অনুগ্রহণ করুন',
            ],
            [
                'key' => 'Close',
                'locale' => 'bd',
                'value' => 'বন্ধ করুন',
            ],
            [
                'key' => 'Cancel',
                'locale' => 'bd',
                'value' => 'াতিল করুন',
            ],
            [
                'key' => 'Confirm',
                'locale' => 'bd',
                'value' => 'নিশ্চিত করুন',
            ],
            [
                'key' => 'OK',
                'locale' => 'bd',
                'value' => 'ঠিক আছে',
            ],
            [
                'key' => 'Save',
                'locale' => 'bd',
                'value' => 'সংরক্ষণ করুন',
            ],
            [
                'key' => 'Search',
                'locale' => 'bd',
                'value' => 'অনুসন্ধান',
            ],
            [
                'key' => 'Filter',
                'locale' => 'bd',
                'value' => 'ফিল্টার',
            ],
            [
                'key' => 'Sort',
                'locale' => 'bd',
                'value' => 'সাজান',
            ],
            [
                'key' => 'Page',
                'locale' => 'bd',
                'value' => 'পৃষ্ঠা',
            ],
            [
                'key' => 'of',
                'locale' => 'bd',
                'value' => 'এর',
            ],
            [
                'key' => 'Items per page',
                'locale' => 'bd',
                'value' => 'প্রতি পৃষ্ঠায়',
            ],
            [
                'key' => 'Showing',
                'locale' => 'bd',
                'value' => 'দেখাচ্ছে',
            ],
            [
                'key' => 'to',
                'locale' => 'bd',
                'value' => 'থেকে',
            ],
            [
                'key' => 'of total',
                'locale' => 'bd',
                'value' => 'োট',
            ],
            [
                'key' => 'Previous',
                'locale' => 'bd',
                'value' => 'আগের',
            ],
            [
                'key' => 'Next',
                'locale' => 'bd',
                'value' => 'পরবর্তী',
            ],
            [
                'key' => 'First',
                'locale' => 'bd',
                'value' => 'প্রথম',
            ],
            [
                'key' => 'Last',
                'locale' => 'bd',
                'value' => 'শেষ',
            ],
            [
                'key' => 'Locales retrieved successfully',
                'locale' => 'bd',
                'value' => 'লোকেলগুলি সফলতাভারভে',
            ],
            [
                'key' => 'Forms retrieved successfully',
                'locale' => 'bd',
                'value' => 'ফর্মসমূহ সফলতাভারভে',
            ],
            [
                'key' => 'Form submitted successfully',
                'locale' => 'bd',
                'value' => 'ফর্ম সফলতাভারভে',
            ],
            [
                'key' => 'Products retrieved successfully',
                'locale' => 'bd',
                'value' => 'পণ্যসমূহ সফলতাভারভে',
            ],
            [
                'key' => 'Product retrieved successfully',
                'locale' => 'bd',
                'value' => 'পণ্যটি সফলতাভারভে',
            ],
            [
                'key' => 'Product not found',
                'locale' => 'bd',
                'value' => 'পণ্যটি পাওয়া যায়',
            ],
            [
                'key' => 'Blogs retrieved successfully',
                'locale' => 'bd',
                'value' => 'ব্লগসমূহ সফলতাভারভে',
            ],
            [
                'key' => 'Blog retrieved successfully',
                'locale' => 'bd',
                'value' => 'ব্লগ সফলতাভারভে',
            ],
            [
                'key' => 'Blog not found',
                'locale' => 'bd',
                'value' => 'ব্লগ পাওয়া যায়',
            ],
            [
                'key' => 'Case studies retrieved successfully',
                'locale' => 'bd',
                'value' => 'কেস স্টাডিসমূহ সফলতাভারভে',
            ],
            [
                'key' => 'Case study retrieved successfully',
                'locale' => 'bd',
                'value' => 'কেস স্টাডি সফলতাভারভে',
            ],
            [
                'key' => 'Case study not found',
                'locale' => 'bd',
                'value' => 'কেস স্টাডি পাওয়া যায়',
            ],
            [
                'key' => 'Categories retrieved successfully',
                'locale' => 'bd',
                'value' => 'ক্যাটাগরিসমূহ সফলতাভারভে',
            ],
            [
                'key' => 'Certifications retrieved successfully',
                'locale' => 'bd',
                'value' => 'সার্টিফিকেশনসমূহ সফলতাভারভে',
            ],
            [
                'key' => 'Certification retrieved successfully',
                'locale' => 'bd',
                'value' => 'সার্টিফিকেশন সফলতাভারভে',
            ],
            [
                'key' => 'Certification not found',
                'locale' => 'bd',
                'value' => 'সার্টিফিকেশন পাওয়া যায়',
            ],
            [
                'key' => 'Featured products retrieved successfully',
                'locale' => 'bd',
                'value' => 'বৈশিষ্ট্যযুক্ত পণ্যসমূহ সফলতাভারভে',
            ],
            [
                'key' => 'News retrieved successfully',
                'locale' => 'bd',
                'value' => 'সংবাদসমূহ সফলতাভারভে',
            ],
            [
                'key' => 'News not found',
                'locale' => 'bd',
                'value' => 'সংবাদ পাওয়া যায়',
            ],
            [
                'key' => 'Pages retrieved successfully',
                'locale' => 'bd',
                'value' => 'পৃষ্ঠাসমূহ সফলতাভারভে',
            ],
            [
                'key' => 'Page retrieved successfully',
                'locale' => 'bd',
                'value' => 'পৃষ্ঠা সফলতাভারভে',
            ],
            [
                'key' => 'Page not found',
                'locale' => 'bd',
                'value' => 'পৃষ্ঠা পাওয়া যায়',
            ],
            [
                'key' => 'Sliders retrieved successfully',
                'locale' => 'bd',
                'value' => 'স্লাইডারসমূহ সফলতাভারভে',
            ],
            [
                'key' => 'Testimonials retrieved successfully',
                'locale' => 'bd',
                'value' => 'সাক্ষ্যদাতাদের তথ্য সফলতাভারভে',
            ],
            [
                'key' => 'App settings retrieved successfully',
                'locale' => 'bd',
                'value' => 'অ্যাপ সেটিংস সফলতাভারভে',
            ],
            [
                'key' => 'App setting field retrieved successfully',
                'locale' => 'bd',
                'value' => 'অ্যাপ সেটিং ক্ষেত্র সফলতাভারভে',
            ],
            [
                'key' => 'App setting not found',
                'locale' => 'bd',
                'value' => 'অ্যাপ সেটিং পাওয়া যায়',
            ],
            [
                'key' => 'Field not found',
                'locale' => 'bd',
                'value' => 'ক্ষেত্র পাওয়া যায়',
            ],
            [
                'key' => 'Language',
                'locale' => 'bd',
                'value' => 'ভাষা',
            ],

            // Product Resource Bangla translations
            [
                'key' => 'Products',
                'locale' => 'bd',
                'value' => 'পণ্যসমূহ',
            ],
            [
                'key' => 'General',
                'locale' => 'bd',
                'value' => 'সাধারণ',
            ],
            [
                'key' => 'Slug',
                'locale' => 'bd',
                'value' => 'স্লাগ',
            ],
            [
                'key' => 'Category',
                'locale' => 'bd',
                'value' => 'বিভাগ',
            ],
            [
                'key' => 'Status',
                'locale' => 'bd',
                'value' => 'অবস্থা',
            ],
            [
                'key' => 'Draft',
                'locale' => 'bd',
                'value' => 'খসড়া',
            ],
            [
                'key' => 'Published',
                'locale' => 'bd',
                'value' => 'প্রকাশিত',
            ],
            [
                'key' => 'Archived',
                'locale' => 'bd',
                'value' => 'আর্কাইভড',
            ],
            [
                'key' => 'Featured Product',
                'locale' => 'bd',
                'value' => 'বৈশিষ্ট্যযুক্ত পণ্য',
            ],
            [
                'key' => 'Translations',
                'locale' => 'bd',
                'value' => 'অনুবাদ',
            ],
            [
                'key' => 'Title',
                'locale' => 'bd',
                'value' => 'শিরোনাম',
            ],
            [
                'key' => 'Short Description',
                'locale' => 'bd',
                'value' => 'সংক্ষিপ্ত বর্ণনা',
            ],
            [
                'key' => 'Detailed Description',
                'locale' => 'bd',
                'value' => 'বিস্তারিত বর্ণনা',
            ],
            [
                'key' => 'Media',
                'locale' => 'bd',
                'value' => 'মিডিয়া',
            ],
            [
                'key' => 'Main Image',
                'locale' => 'bd',
                'value' => 'প্রধান ছবি',
            ],
            [
                'key' => 'Gallery',
                'locale' => 'bd',
                'value' => 'গ্যালারী',
            ],
            [
                'key' => 'Video Embeds',
                'locale' => 'bd',
                'value' => 'ভিডিও এম্বেড',
            ],
            [
                'key' => 'Type',
                'locale' => 'bd',
                'value' => 'ধরন',
            ],
            [
                'key' => 'Embed URL',
                'locale' => 'bd',
                'value' => 'এম্বেড URL',
            ],
            [
                'key' => 'Self-hosted File',
                'locale' => 'bd',
                'value' => 'সেলফ-হোস্টেড ফাইল',
            ],
            [
                'key' => 'URL',
                'locale' => 'bd',
                'value' => 'URL',
            ],
            [
                'key' => 'Video File',
                'locale' => 'bd',
                'value' => 'ভিডিও ফাইল',
            ],
            [
                'key' => 'Downloads',
                'locale' => 'bd',
                'value' => 'ডাউনলোড',
            ],
            [
                'key' => 'Technical Specs',
                'locale' => 'bd',
                'value' => 'প্রযুক্তিগত স্পেস',
            ],
            [
                'key' => 'Technical Specifications',
                'locale' => 'bd',
                'value' => 'প্রযুক্তিগত বিবরণ',
            ],
            [
                'key' => 'Key',
                'locale' => 'bd',
                'value' => 'কী',
            ],
            [
                'key' => 'Value',
                'locale' => 'bd',
                'value' => 'মান',
            ],
            [
                'key' => 'Tags',
                'locale' => 'bd',
                'value' => 'ট্যাগস',
            ],
            [
                'key' => 'Tag',
                'locale' => 'bd',
                'value' => 'ট্যাগ',
            ],
            [
                'key' => 'Related Products',
                'locale' => 'bd',
                'value' => 'সম্পর্কিত পণ্য',
            ],
            [
                'key' => 'SEO',
                'locale' => 'bd',
                'value' => 'এসইও',
            ],
            [
                'key' => 'Meta Title',
                'locale' => 'bd',
                'value' => 'মেটা শিরোনাম',
            ],
            [
                'key' => 'Meta Description',
                'locale' => 'bd',
                'value' => 'মেটা বর্ণনা',
            ],
            [
                'key' => 'Meta Keywords',
                'locale' => 'bd',
                'value' => 'মেটা কীওয়ার্ড',
            ],
            [
                'key' => 'Canonical URL',
                'locale' => 'bd',
                'value' => 'ক্যানোনিকাল URL',
            ],
            [
                'key' => 'Image',
                'locale' => 'bd',
                'value' => 'ছবি',
            ],
            [
                'key' => 'Status updated',
                'locale' => 'bd',
                'value' => 'অবস্থা আপডেট করা হয়েছে',
            ],
            [
                'key' => 'Product status has been changed to',
                'locale' => 'bd',
                'value' => 'পণ্যের অবস্থা পরিবর্তন করা হয়েছে',
            ],
            [
                'key' => 'Share',
                'locale' => 'bd',
                'value' => 'শেয়ার',
            ],
            [
                'key' => 'URL Preview',
                'locale' => 'bd',
                'value' => 'URL প্রিভিউ',
            ],
            [
                'key' => 'This is the URL that will be included in your social media posts',
                'locale' => 'bd',
                'value' => 'এই URL আপনার সোশ্যাল মিডিয়া পোস্টে অন্তর্ভুক্ত করা হবে',
            ],
            [
                'key' => 'Content URL',
                'locale' => 'bd',
                'value' => 'কন্টেন্ট URL',
            ],
            [
                'key' => 'This URL will be shared on the selected social media platforms',
                'locale' => 'bd',
                'value' => 'এই URL নির্বাচিত সোশ্যাল মিডিয়া প্ল্যাটফর্মে শেয়ার করা হবে',
            ],
            [
                'key' => 'Share to Platforms',
                'locale' => 'bd',
                'value' => 'প্ল্যাটফর্মে শেয়ার করুন',
            ],
            [
                'key' => 'Facebook',
                'locale' => 'bd',
                'value' => 'ফেসবুক',
            ],
            [
                'key' => 'Twitter (X)',
                'locale' => 'bd',
                'value' => 'টুইটার (X)',
            ],
            [
                'key' => 'LinkedIn',
                'locale' => 'bd',
                'value' => 'লিংকডইন',
            ],
            [
                'key' => 'Select which social media platforms to share this product on',
                'locale' => 'bd',
                'value' => 'এই পণ্যটি কোন সোশ্যাল মিডিয়া প্ল্যাটফর্মে শেয়ার করতে চান তা নির্বাচন করুন',
            ],
            [
                'key' => 'Product shared successfully!',
                'locale' => 'bd',
                'value' => 'পণ্য সফলভাবে শেয়ার করা হয়েছে!',
            ],
            [
                'key' => 'The product has been queued for sharing to selected social media platforms.',
                'locale' => 'bd',
                'value' => 'পণ্যটি নির্বাচিত সোশ্যাল মিডিয়া প্ল্যাটফর্মে শেয়ারের জন্য সারিবদ্ধ করা হয়েছে।',
            ],
            [
                'key' => 'Share Product',
                'locale' => 'bd',
                'value' => 'পণ্য শেয়ার করুন',
            ],
            [
                'key' => 'Share Now',
                'locale' => 'bd',
                'value' => 'এখনই শেয়ার করুন',
            ],
            [
                'key' => 'Edit',
                'locale' => 'bd',
                'value' => 'সম্পাদনা',
            ],
            [
                'key' => 'Delete',
                'locale' => 'bd',
                'value' => 'মুছে ফেলুন',
            ],
            [
                'key' => 'Delete Selected',
                'locale' => 'bd',
                'value' => 'নির্বাচিত মুছে ফেলুন',
            ],
            [
                'key' => 'Deleted',
                'locale' => 'bd',
                'value' => 'মুছে ফেলা হয়েছে',
            ],
            [
                'key' => 'items deleted successfully.',
                'locale' => 'bd',
                'value' => 'আইটেম সফলভাবে মুছে ফেলা হয়েছে।',
            ],
            [
                'key' => 'Change Status',
                'locale' => 'bd',
                'value' => 'অবস্থা পরিবর্তন করুন',
            ],
            [
                'key' => 'Status Updated',
                'locale' => 'bd',
                'value' => 'অবস্থা আপডেট করা হয়েছে',
            ],
            [
                'key' => 'Selected items have been updated to',
                'locale' => 'bd',
                'value' => 'নির্বাচিত আইটেমগুলি আপডেট করা হয়েছে',
            ],
        ];

        foreach ($translations as $translation) {
            UiTranslation::query()->updateOrCreate(
                [
                    'key' => $translation['key'],
                    'locale' => $translation['locale'],
                ],
                [
                    'value' => $translation['value'],
                ]
            );
        }
    }
}
