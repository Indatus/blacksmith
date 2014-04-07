# Blacksmith


<img height="300" align="left" src="https://s3-us-west-2.amazonaws.com/oss-avatars/blacksmith_round_readme.png">


Blacksmith is a code generation tool which automates the creation of common files that you'd typically create for each entity in your application.

Blacksmith works nicely with the [Laravel](http://laravel.com) PHP framework but does not actually *depend* on Laravel or extend an Artisan task, it is stand alone.  This allows you to use it for code generation from a template anywhere on your filesystem.  In fact, while some of the [generator specific template variables](#specific-template-variables) and post-generation tasks of the [Intelligent Laravel Generators](#intelligent-generators) are Laravel specific; if the code you want to generate uses studly case and snake case naming, you could generate any code you wanted: Java, JavaScript etc.

---

Out of the box for the 1.0.0 release Blacksmith ships with a [Laravel hexagonal architecture](https://github.com/brianwebb01/hexagonal-laravel-experiment) configuration and templates. Why? Because this is what I needed first.

> **Note:** If you want to get started with the hexagonal architecture referenced above for a new app.  Use [Foreman](https://github.com/indatus/foreman) with [this example config](https://gist.github.com/brianwebb01/9201450) (changing the 'from' paths appropriatley).

If you don't like the templates + configuration shipped with Blacksmith, don't worry you can make your own custom templates tell Blacksmith how to load them with a JSON config file. Then you just pass that config file's path as an optional argument to Blacksmith's `generate` command and presto, your custom templates and config are used.

> **Attribution:** Blacksmith was inspired by, and even borrows some code from [Jeffrey Way's](https://twitter.com/jeffrey_way) fantastic MIT licensed [Laravel-4-Generators](https://github.com/JeffreyWay/Laravel-4-Generators) package

## README Contents

* [What does it do](#what-does-it-do)
* [Installation](#installation)
  * [Download the PHAR](#install-download)
  * [Compile from source](#install-compile)
  * [Updating Blacksmith](#updating)
* [Usage](#usage)
* [Getting Started](#getting-started)
* [The Config File](#config-file)
* [Templates](#templates)
* [Template Variables](#template-variables)
  * [Standard Variables](#standard-template-variables)
  * [Generator Specific Variables](#specific-template-variables)
* [Intelligent Laravel Generators](#intelligent-generators)

<a name="what-does-it-do" />
## What does it do

Out of the box Blacksmith ships with the following generators:

**Single Generators** (generates one file)

* `model`
* `controller`
* `seed`
* `migration_create`
* `view_create`
* `view_update`
* `view_show`
* `view_index`
* `form`
* `test_unit`
* `test_functional`
* `service_creator`
* `service_updater`
* `service_destroyer`
* `validator`

**Aggregate Generators** (calls 1 or more other generators)
* `scaffold` - calls all generators above with the given arguments

<a name="installation" />
## Installation


<a name="install-download" />
### Download the PHAR
The simplest method of installation is to simply [download the blacksmith.phar](https://github.com/indatus/blacksmith/raw/master/blacksmith.phar) file from this repository.

<a name="mv-easy-access" />
> **(Optional) Move and set permissions**
Now if you'd like you can move the PHAR to `/usr/local/bin` and rename it to `blacksmith` for easy access. You may need to grant the file execution privileges (`chmod +x`) before running commands.

<a name="install-compile" />
### Compile from source
To compile the blacksmith.phar file yourself, clone this repository and run the `box build` command. To run box commands, you must install [kherge/Box](https://github.com/kherge/Box).

[See optional move and permissions above](#mv-easy-access).

<a name="updating" />
### Updating Blacksmith

To update Blacksmith, you may use the `blacksmith self-update` command.


<a name="usage" />
## Usage

Blacksmith generation commands have the following signature:

    blacksmith generate {entity} {generator} [config_file] --fields="field1:type, field2:type"

In the example above the `config_file` argument and `--fields` option are both optional.  _{entity}_ is the name of the entity in your use case that you are generating code for, and _{generator}_ is the [generator](#what-does-it-do) you want to run.

To declare fields, use a comma-separated list of key:value:option sets, where key is the name of the field, value is the column type, and option is a way to specify indexes or other things like unique or nullable. Here are some examples:

* `--fields="first:string, last:string"`
* `--fields="age:integer, yob:date"`
* `--fields="username:string:unique, age:integer:nullable"`
* `--fields="name:string:default('John'), email:string:unique:nullable"`
* `--fields="username:string[30]:unique, age:integer:nullable"`

Please make note of the last example, where we specify a character limit: `string[30]`. This will produce `$table->string('username', 30)->unique();`

An example without placeholders might look like this:

    blacksmith generate User scaffold ~/blksmth/cfg.json --fields="username:string:unique, age:integer:nullable"

> **Note:** Blacksmith works from your current working directory, so if you have a template that gets written to a releative path, that will be relative to your current directory when you run Blacksmith. For **Laravel** this directory should be the application root.

<a name="getting-started" />
## Getting Started

While Blacksmith comes packaged with templates and base configuration file, it's likely you'll want to [download the templates](src/lib/Generators/templates) and customize them to suit your needs.  This will also require you create your own config file which you can read more about in the next section.

> As you begin to generate you may notice some interfaces referenced in templates that were not generated.  This is not a mistake.  You should create these interfaces to suit your application.

<a name="config-file" />
## The Config File

The Blacksmith config file is written in JSON and has a specific format.  Blacksmith will validate the file's format before use and provide errors if there is a mis-configuration.

The config file has a root level item for each single generator, and a root level item for *config_type*. The *config_type* element leaves the door open for subsequent configurations like "mvc" that would require a different collection of generator keys to be present.

Here is a **partial** view of the template, for a full template checkout the default [here](https://github.com/Indatus/blacksmith/blob/v1.0.0/src/lib/Generators/templates/hexagonal/config.json):

<a name="example-config" />

```json
{
    "config_type": "hexagonal",

    "model": {
        "template": "model.txt",
        "directory": "app/models",
        "filename": "{{Entity}}.php"
    },
    "controller": {
        "template": "controller.txt",
        "directory": "app/controllers",
        "filename": "{{Entities}}Controller.php"
    },
    "migration_create": {
        "template": "migration_create.txt",
        "directory": "app/database/migrations",
        "filename": "{{migration_timestamp}}_create_{{collection}}_table.php"
    },
    "view_show": {
        "template": "view_show.txt",
        "directory": "app/views/{{collection}}",
        "filename": "show.blade.php"
    },
}
```

The key take away here is that for each singular generator there are sub elements for **template**, **directory**, and **filename**.

* **template** - the template file to use for this generator relative to the config file
* **directory** - the directory where the final parsed template should be written, relative to the directory you were in when Blacksmith was executed.
* **filename** - the filename the parsed template should be saved as

> **Note:** if the destination directories do not exist Blacksmith will create them.

To skip a generator, set it's configuration to `false`.  This can be useful to skip generating views when you're building an API.

```json
    "migration_create": false,
```

You can also see in the [partial config file](#example-config) example that you can use [template variables](#template-variables) provided by the generator in both the *directory* and *filename* values of the configuration.

<a name="templates" />
## Templates

Blacksmith templates use [Mustache](http://mustache.github.io/) syntax, otherwise they are just plain text files.  [See below](#template-variables) for the available variables that can be used.

The variables made available for each generator can be used in the **generator template file** or within the JSON config file in the **directory**, and **filename** values.

<a name="template-variables" />
## Template Variables

<a name="standard-template-variables" />
### Standard Variables

Each Blacksmith generator will always have the following standard variables available for use in the **generator template** or in the config's **directory**, and **filename** values.

Variable | Description | Output Example
--- | --- | ---
`Entity` | Singular studly cased entity name | Order
`Entities` | Plural studly cased entity name | Orders
`collection` | Plural underscore cased entity name | orders
`instance` | Singular underscore cased entity name | order
`fields` | Associative multi-dimensional array of field names and attributes | `['name' => ['type' => 'string']]`
`year` | The current year | 2014

<a name="specific-template-variables" />
### Generator Specific Variables

Generator specific variables may only be available in certain generators because the use is specific:

Variable | Description | Generators | Output Example
--- | --- | --- | ---
`headings` | Array of field names that can be used as table headings | view_index<br />view_show | `['name', 'age']`
`cells` | Array of code snippets that can be used to ouptut values in table cells | view_index<br />view_show | `["\$user->name", "\$user->age"]`
`mock_attributes` | Array of fields set with mock data | functional_test | `["'name' => 'dreamcatcher',",]`
`columns` | Array of schema builder column creation code snippets | migration_create | `["\$table->string('name');"]`
`migration_timestamp` | Timestamp used for migration filenames | migration_create | 2014_03_13_042956
`form_rows` | Array of element + label Laravel form builder code for the passed in fields | form | _see below_

`form_rows` Output Example:

```php
[
    [
        'label' => "{{ Form::label('name', 'Name:') }}",
        'element' => "{{ Form::text('name') }}"
    ]
]
```

> **Note:** with the exception of `migration_timestamp` all the generator specific variables are arrays.  The content and count will vary as it is based on the `--fields` that were passed in.

<a name="intelligent-generators" />
## Intelligent Laravel Generators

Outside of template variables some of the generators have laravel specific actions that are taken after the template is generated and written out IF conditions are right.  The table below describes each use case:

Generator | Extra Functionality
--- | ---
`migration_create` | Tests to see if `app/database/seeds/DatabaseSeeder.php` exists relative to the current directory. If so it checks that the newly generated seeder isn't already present in `DatabaseSeeder.php`.  If the file exists, and entry is not present it will be added.
`scaffold` | Tests to see if `app/routes.php` exists relative to the current directory. If so the routes file will be appended with a `Route::resource()` entry for the newly created resourceful controller.