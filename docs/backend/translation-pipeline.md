# Translation Pipeline

## Overview

This document describes the lifecycle of translations in the application, following industry-standard separation of concerns.

**Principle:** Filament handles UI, Models handle data, Traits handle translations.

---

## CREATE Flow

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. FILAMENT BUILDS THE FORM                                     │
│    - Defines form fields (title.en, title.bd, etc.)             │
│    - Presents form to user                                      │
│    - "Here is the form, fill and give it to me"                 │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│ 2. USER FILLS & SUBMITS                                         │
│    - User fills all locale fields                               │
│    - Submits form to Filament                                   │
│    - "Here is the filled form"                                  │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│ 3. FILAMENT RECEIVES DATA                                       │
│    - Validates form input                                       │
│    - Tells Model: "See what I got from user, can you store it?" │
│    - Calls: $model->fill($data); $model->save();                │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│ 4. MODEL RECEIVES DATA                                          │
│    - Model says: "Ah, here we go, let me store the record"      │
│    - Eloquent prepares to save                                  │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│ 5. TRAIT INTERCEPTS (saving event)                              │
│    - Trait knocks: "Hey, you got data? Let me see..."           │
│    - Checks model's $translatable array                         │
│    - Finds: "Whahah, here is something for me!"                 │
│    - Separates translatable fields from non-translatable        │
│    - Keeps translatable fields in memory                        │
│    - Prepares CLEAN payload for model (without translatable)    │
│    - Tells model: "I took my fields, you store yours first"     │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│ 6. MODEL SAVES CLEAN DATA                                       │
│    - INSERT INTO products (title, slug, status, ...)            │
│    - Model gets ID: $model->id = 42                             │
│    - Model tells trait: "Here is your ID, do your work"         │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│ 7. TRAIT SAVES TRANSLATIONS (saved event)                       │
│    - Trait prepares data with model's ID                        │
│    - INSERT INTO translations (translatable_id=42, ...)         │
│    - Stores all locale values                                   │
│    - Tells model: "I finished my job, you are free to go"       │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│ 8. MODEL INFORMS FILAMENT                                       │
│    - "I stored the data, here is the record ID"                 │
│    - Filament sees creation successful                          │
│    - Redirects user to edit page                                │
└─────────────────────────────────────────────────────────────────┘
```

---

## EDIT Flow

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. FILAMENT REQUESTS RECORD                                     │
│    - User navigates to edit page                                │
│    - Filament tells model: "I have an ID, check DB for it"      │
│    - Calls: Product::with('translations')->find(42)             │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│ 2. MODEL FETCHES RECORD                                         │
│    - SELECT * FROM products WHERE id = 42                       │
│    - Model pulls the record from DB                             │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│ 3. TRAIT INTERCEPTS                                             │
│    - Trait comes: "You got something? Let me see..."            │
│    - Checks if model has translatable fields                    │
│    - Finds translatable fields in $translatable array           │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│ 4. TRAIT LOADS ALL TRANSLATIONS (1 query)                       │
│    - SELECT * FROM translations                                 │
│        WHERE translatable_id = 42                               │
│        AND translatable_type = 'App\Models\Product'             │
│    - Puts all translations into model                           │
│    - Tells model: "Now you can go wherever you want"            │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│ 5. MODEL RETURNS TO FILAMENT                                    │
│    - "Hey, see what I got for your given ID"                    │
│    - Returns model with translations loaded                     │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│ 6. FILAMENT BUILDS EDIT FORM                                    │
│    - Receives model with all translations                       │
│    - Builds form with locale fields pre-filled                  │
│    - Presents to user                                           │
└─────────────────────────────────────────────────────────────────┘
```

---

## Key Principles

| Principle | Implementation |
|-----------|---------------|
| **Separation of Concerns** | Filament = UI, Model = Data, Trait = Translations |
| **No Filament Translation Logic** | No `mutateFormDataBeforeFill()` or `mutateFormDataBeforeSave()` |
| **Trait Intercepts Automatically** | Uses Eloquent events (`saving`, `saved`) |
| **Single Query for Loading** | All translations loaded in 1 query via eager loading |
| **Clean Model Data** | Main columns store default locale only |
| **Type Detection** | Auto-detects: string, text, rich_text, file_path, json |

---

## File Locations

| File | Responsibility |
|------|---------------|
| `app/Traits/HasTranslations.php` | All translation logic |
| `app/Models/Translation.php` | Translation model |
| `app/Models/{Product,Blog,News,etc}.php` | Define `$translatable` array |
| `app/Filament/**/*Resource.php` | ONLY form field definitions |
| `app/Filament/**/Pages/*Record.php` | NO translation mutation |

---

## Event Flow

### Create
```
saving() → Trait extracts translatable fields
saved()  → Trait saves translations (model has ID now)
```

### Update
```
saving() → Trait extracts & saves translations
saved()  → Nothing (already handled)
```

### Read
```
Model::with('translations') → Eager loads all translations
getAttribute() → Returns locale-specific value
```
