<?php

namespace App\Filament\Admin\Resources\ContactSubmissionResource\Form;

use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContactSubmissionForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')
                ->label(__('Name'))
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->label(__('Email'))
                ->email()
                ->required()
                ->maxLength(255),
            TextInput::make('phone')
                ->label(__('Phone'))
                ->nullable()
                ->maxLength(50),
            TextInput::make('country')
                ->label(__('Country'))
                ->nullable()
                ->maxLength(100),
            TextInput::make('place')
                ->label(__('Company'))
                ->nullable()
                ->maxLength(255),
            TextInput::make('subject')
                ->label(__('Subject'))
                ->required()
                ->maxLength(500),
            Textarea::make('message')
                ->label(__('Message'))
                ->required()
                ->rows(6)
                ->columnSpanFull(),
            Select::make('status')
                ->label(__('Status'))
                ->options([
                    'new' => __('New'),
                    'in_progress' => __('In Progress'),
                    'resolved' => __('Resolved'),
                    'closed' => __('Closed'),
                ])
                ->required(),
            Select::make('priority')
                ->label(__('Priority'))
                ->options([
                    'low' => __('Low'),
                    'medium' => __('Medium'),
                    'high' => __('High'),
                    'urgent' => __('Urgent'),
                ])
                ->required(),
            Select::make('assigned_to')
                ->label(__('Assigned To'))
                ->options(User::pluck('name', 'id'))
                ->searchable()
                ->nullable(),
            Select::make('source')
                ->label(__('Source'))
                ->options([
                    'contact_page' => __('Contact Page'),
                    'footer' => __('Footer'),
                    'popup' => __('Popup'),
                    'support_page' => __('Support Page'),
                    'quote_request' => __('Quote Request'),
                    'api' => __('API'),
                ])
                ->required(),
            Textarea::make('admin_notes')
                ->label(__('Admin Notes'))
                ->nullable()
                ->rows(4)
                ->columnSpanFull(),
            Select::make('extras.resource_type')
                ->label(__('Resource Type'))
                ->options([
                    'product' => __('Product'),
                    'blog' => __('Blog'),
                    'news' => __('News'),
                    'project' => __('Project'),
                    'page' => __('Page'),
                    'case_study' => __('Case Study'),
                    'brand' => __('Brand'),
                    'category' => __('Category'),
                ])
                ->nullable()
                ->searchable(),
            TextInput::make('extras.resource_id')
                ->label(__('Resource ID'))
                ->nullable()
                ->numeric(),
            DateTimePicker::make('responded_at')
                ->label(__('Responded At'))
                ->nullable(),
            DateTimePicker::make('resolved_at')
                ->label(__('Resolved At'))
                ->nullable(),
        ])->columns(2);
    }
}
