# Snowflake for Winter CMS

## Fork Information and Acknowledgments

This plugin was forked from [skripteria/snowflake](https://github.com/skripteria/snowflake) to [spanjaan/snowflake](https://github.com/spanjaan/snowflake). This fork aims to enhance and extend the functionalities of the original plugin to better meet the needs of various user types within Winter CMS.

A big thank you to all contributors and the Winter CMS community for their continued support and inspiration!

## Version History

- **1.0.5:** Initial release

## Installation with Composer

To install Snowflake, run the following commands:

```sh
composer require spanjaan/wn-snowflake-plugin

php artisan winter:up
```

## Why a New "CMS" Within a CMS?

In real-life scenarios, there are at least four different user types that need to be considered:

- **Technical Developer:** Has some knowledge of how to build custom functionality.
- **Web Designer:** Primarily interested in web design rather than backend development.
- **Web-Interested End User:** Has a vision of how things should look and tries to make it happen. Not afraid of experimenting with customization, similar to a typical WordPress user.
- **Non-Technical User:** Occasionally needs to do some content editing due to job or business requirements.

The first three user types are usually considered when discussing how a CMS should function. However, the last user type is often overlooked. When developing a site for a paying customer, the non-technical user is the one you will most likely deal with in the end.

As a result, you need to make content management foolproof and hide everything except the specific pieces of content that need to be managed.

Snowflake addresses this by leveraging the power of the Winter CMS backend framework.

## Using Snowflake

Once installed, you first need to add the Snowflake component to any CMS Layout you want to use it with. This will enable the Snowflake Twig filter on the Layout itself and any Page using this Layout.

On these CMS pages, you can now add content variables using the `'sf'` Twig filter, for example:

```html
<h1>{{ 'my_headline' | sf('text', 'My awesome headline', 'Main headline of this page.') }}</h1>
```

The first part (`'my_headline'`) is the Snowflake key used to render the content. The Snowflake key acts like a normal Twig variable and references the content.

Every Snowflake key must be unique within a given Page but may conflict with keys from other Pages. When adding Snowflake keys to a Layout, name collisions with Pages can occur. It is generally recommended to prefix Snowflake keys in Layouts (e.g., `'layout_my_headline'`).

The `'sf'` filter takes up to three arguments:

1. **Argument 1**: Defines the type of content. This controls which backend widget is used for content management.
2. **Argument 2 (optional)**: Allows setting a default value for the content. The default value has no effect on the types 'image', 'file', 'date', 'mediaimage', and 'mediafile'.
3. **Argument 3 (optional)**: Adds a description for the user responsible for content management.

*Note: The characters ',' (comma) and '|' (pipe) must not be used within arguments.*

Currently, Snowflake supports seven standard types and five special ones.

### Standard Types:

- `text` (simple text input, e.g., for headlines)
- `color` (Winter CMS color picker)
- `markdown` (Winter CMS markdown editor)
- `richeditor` (Winter CMS rich text editor)
- `code` (Winter CMS code editor)
- `date` (Winter CMS date picker)
- `textarea` (plain textarea field)

### Special Types:

- **`image`:** 

  Used to control images and utilizes the Winter CMS image upload widget. It requires two values for rendering: the image path and the `alt` attribute. To handle this, the variable must pass two values.

  Example:

  ```html
  <img src="{{ 'my_image' | sf('image', '', 'This is the hero image on this page') }}" alt="{{ my_image___alt }}">
  ```

  *Note: The `'sf'` filter is only added once in the `src` attribute; the `alt` attribute uses the same key with the suffix `'__alt'`.*

- **`mediaimage`:**

  Similar to `'image'`, but `'mediaimage'` uses the Winter Media Library as the image source:

  ```html
  <img src="{{ 'my_image' | sf('mediaimage', '', 'This is the hero image on this page') }}" alt="{{ my_image___alt }}">
  ```

- **`file`:**

  Used to control file uploads. Like `image`, it uses two values: the file path and the filename displayed in the link.

  Example:

  ```html
  <a href="{{ 'my_file' | sf('file', '', 'This is my uploaded file') }}">{{ my_file__name }}</a>
  ```

- **`mediafile`:**

  Similar to `'file'`, but `'mediafile'` uses the Winter Media Library as the file source:

  ```html
  <a href="{{ 'my_file' | sf('mediafile', '', 'This is my uploaded file') }}">{{ my_file__name }}</a>
  ```

- **`link`:**

  Used for internal Winter CMS links. The `'link'` type allows content managers to simply copy the full URL from the browser window without worrying about proper formatting. It is automatically converted into a clean relative link.

## Synchronizing with the Snowflake Backend

If you are using the Winter CMS backend to edit your code, simply save your Page or Layout, and Snowflake will automatically create or update the respective records in the database.

Once a Snowflake key is removed (or renamed), it is handled as an unused database record based on the following logic:

- **If existing content is present in the record:** It is retained.
- **If no content exists:** It is deleted.

Alternatively, you can use a console command to sync all CMS Pages and Layouts:

```sh
php artisan snowflake:sync
```

To clean up all unused Snowflake keys (caution: this also deletes the associated content), use:

```sh
php artisan snowflake:sync --cleanup
```

---
