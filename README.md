# JsonToTemplate

JsonToTemplate is a MediaWiki extension that allows converting JSON data into MediaWiki template calls. It is useful for dynamically and modularly representing structured data on MediaWiki pages.

## Features

- Converts JSON into MediaWiki template calls.
- Supports nested objects, simple lists, and complex structures.
- Automatically generates sub-templates for each level of the JSON.
- Easy to integrate and use in any MediaWiki installation.

## Requirements

- MediaWiki 1.31 (It has only been tested in this version).
- PHP 7.0 or higher.

## Installation

1. Clone this repository into the `extensions` directory of your MediaWiki installation:

   ```bash
   git clone https://github.com/your-username/JsonToTemplate.git extensions/JsonToTemplate
   ```

2. Add the following line to your `LocalSettings.php` file:

   ```php
   wfLoadExtension( 'JsonToTemplate' );
   ```

  or
  
  ```php
  require_once "$IP/extensions/JsonToTemplate/JsonToTemplate.php";
  ```

3. Ensure the directory permissions are correct so MediaWiki can access the files.

4. Clear MediaWiki's cache to load the extension:

   ```bash
   php maintenance/update.php
   ```

## Usage

### Basic Syntax

To use the extension, add a JSON block inside the `<json>` tag on a MediaWiki page. Specify the name of the main template using the `template` attribute.

```html
<json template="TemplateName">
{
    "key1": "value1",
    "key2": "value2",
    "list": [
        "item1",
        "item2",
        "item3"
    ],
    "object": {
        "subkey1": "subvalue1",
        "subkey2": "subvalue2"
    }
}
</json>
```

### Example

Given a JSON like this:

```json
{
    "firstname": "John",
    "lastname": "Doe",
    "details": {
        "age": "30",
        "height": "1.85",
        "hobbies": [
            "coding",
            "reading",
            "gaming"
        ],
        "countries": [
            "Colombia",
            "Spain",            
            "France"
        ]
    }
}
```

And the Mediawiki call:

```html
<json template="person">
{
    "firstname": "John",
    "lastname": "Doe",
    "details": {
        "age": "30",
        "height": "1.85",
        "hobbies": [
            "coding",
            "reading",
            "gaming"
        ],
        "countries": [
            "Colombia",
            "Spain",            
            "France"
        ]
    }
}
</json>
```

You must put the name of the main template in the "template" attribute. In this case <json template="person">.

Then you must create a template for each dimension of the json. The name of the Mediawiki Template will be the same as the key of the list or array:

#### Person template:

```html
<div class="data-container">
<b>First name</b>: {{{firstname}}}<br>
<b>Last name</b>: {{{lastname}}}<br>
{{{details}}}
</div>
```

#### "details" template:
```html
<div style="border: 1px solid #000; padding:10px">
<strong>Height</strong>: {{{height}}}<br>
<strong>Age</strong>: {{{age}}}<br>
<strong>Hobbies</strong>:
<ul>
{{{hobbies}}}
</ul>
<strong>Countries</strong>:
<ul>
{{{countries}}}
</ul>
</div>
```

List or array templates should only contain the "item" layout, using the {{{item}}} variable:

#### "hobbies" template:
```html
<li style="color:red">{{{item}}}</li>
```

#### "countries" template:
```html
<span style="color:blue">{{{item}}}</span><br>
```

### Output

The extension will generate the following output for MediaWiki:

```plaintext
{{person
    |firstname=Jhon
    |lastname=Doe
    |details={{details
        |age=30
        |height=1.85
        |hobbies={{hobbies|item=coding}}{{hobbies|item=reading}}{{hobbies|item=gaming}}
        |countries={{countries|item=Colombia}}{{countries|item=Spain}}{{countries|item=France}}
    }}
}}
```

### Error Messages

If the JSON is invalid, an error message will be displayed in the configured language of MediaWiki. For example:

- In English: `Error: Invalid JSON`
- In Spanish: `Error: JSON inválido`

## Customization

You can customize the templates used to represent the data. Each key in the JSON becomes a template with the same name. For example, for the key `"hobbies"`, the template `hobbies` will be called.

## Contributing

Contributions are welcome! If you find a bug or have an idea to improve the extension, feel free to open an issue or submit a pull request on this repository.

## License

This extension is licensed under the [MIT License](LICENSE).

## Author

Created by Diego F. Gómez.
