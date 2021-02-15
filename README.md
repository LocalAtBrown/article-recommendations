# Article Recommendations Modules

[![Commitizen friendly](https://img.shields.io/badge/commitizen-friendly-brightgreen.svg)](http://commitizen.github.io/cz-cli/)

## Development

Serve the files locally with `cmd + P` and select `PHP Server: Serve project` in VS Code.

## Installing the command line tool

[Commitizen](https://github.com/commitizen/cz-cli) is currently tested against
node 10 and 12 although it may work in
older node. You should also have npm 6
or greater.

```sh
npm install -g commitizen
```

## Using the command line tool

### If your repo is [Commitizen-friendly]

Simply use `git cz` or just `cz` instead of `git commit` when committing. You can also use `git-cz`, which is an alias for `cz`.

_Alternatively_, if you are using **NPM 5.2+** you can [use `npx`](https://medium.com/@maybekatz/introducing-npx-an-npm-package-runner-55f7d4bd282b) instead of installing globally:

```sh
npx cz
```

When you're working in a Commitizen friendly repository, you'll be prompted to fill in any required fields and your commit messages will be formatted according to the standards defined by project maintainers.

[![Add and commit with Commitizen](https://github.com/commitizen/cz-cli/raw/master/meta/screenshots/add-commit.png)](https://github.com/commitizen/cz-cli/raw/master/meta/screenshots/add-commit.png)

## Conventional commit messages as a global utility

Install `commitizen` globally, if you have not already.

```sh
npm install -g commitizen
```

Install your preferred `commitizen` adapter globally, for example [`cz-conventional-changelog`](https://www.npmjs.com/package/cz-conventional-changelog)

```sh
npm install -g cz-conventional-changelog
```

Create a `.czrc` file in your `home` directory, with `path` referring to the preferred, globally installed, `commitizen` adapter

```sh
echo '{ "path": "cz-conventional-changelog" }' > ~/.czrc
```

You are all set! Now `cd` into any `git` repository and use `git cz` instead of `git commit` and you will find the `commitizen` prompt.

Protip: You can use all the `git commit` `options` with `git cz`, for example: `git cz -a`.

> If your repository is a [nodejs](https://nodejs.org/en/) project, making it [Commitizen-friendly] is super easy.
