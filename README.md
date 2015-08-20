# Bookdown2Mkdocs

Convert your `bookdown.json` files to `mkdocs.yml` so that you can publish
documentation to rtfd.org:

```bash
$ cd project-with-bookdown
$ path/to/bin/bookdown2mkdocs.php convert doc/bookdown.json \
> --site-name=my-project \
> --repo-url=http://example.com/project \
> --copyright-author="Me Me Me" \
> --copyright-url=http://example.com
```

> ## Caveats
>
> - The command assumes that your documentation is in `doc/book/`.
> - The command assumes that you want rendered documentation in `doc/html/`.
> - The command will create a symlink `doc/book/index.md` pointing to the
>   project `README.md` if such a symlink does not exist. This is because you
>   cannot have `index` pages in bookdown; those are reserved for auto-generated
>   TOCs.
> - References to remote `bookdown.json` files will not work, only local files.
> - The command *will* overwrite `mkdocs.yml`.

## Installation

Use [Composer](https://getcomposer.org) to install the tool:

```bash
$ composer global require phly/bookdown2mkdocs
```

Tip: add `$HOME/.composer/vendor/bin` to your `$PATH`.

## Usage

Excecute the command in the root of your project.

> bookdown2mkdocs.php convert [<bookdown-path>] --site-name= --repo-url= --copyright-url= --copyright-author= [--mkdocs=]

### Arguments

- `[<bookdown-path>]`: Path to bookdown.json; if not present, assumes doc/bookdown.json
- `--site-name=`: Site/project name; typically used as the subdomain in rtfd.org
- `--repo-url=`: Repository URI (linked from generated docs)
- `--copyright-url=`: URL associated with the copyright holder
- `--copyright-author=`: Copyright holder/author
- `[--mkdocs=]`: Additional default configuration for mkdocs, as a JSON string
