# 1. Summary

- Fork the project
- Create a branch from `master` for your fix/feature/etc preferably following Git Flow naming (see below)
- When done, commit locally
- Execute and adapt the Behat tests to make sure nothing is broken (see below)
- Create a pull request (PR) explaining what you did, and/or referencing the issue #id you have worked on









# 2. Naming your branches (Git Flow)

It is considered a good practice to use Git Flow branch names when contributing to open source projects.
It consists in a keyword prefix with a trailing slash (ex: `feature/`) and your contribution name, e.g.: `feature/make-coffee-automatically`.
Here are some other meaningful branch prefixes that are commonly used:

## 2.1 Local tests

`develop`: Use locally. This is a main branch where all the development happens. Feature and fix branches usually branch off from here.
**Do not try to PR on master!**


## 2.2 Big changes

`feature/`: Used to add a new feature on the project

`release/`: Branches off from develop when the codebase reaches a point where it's ready for release. It’s used for final polishing and bug fixing before the code is merged into master and tagged with a version number. We don't use release numbers yet, but we should start doing so.


## 2.3 Small evolutions

`fix/`: Used for making quick fixes to the production version

`enhancement/` or `improvement/`: For small but meaningful changes that enhance features without adding completely new functionality.

`refactor/`: For code refactoring that doesn’t necessarily add new features or fix bugs but improves the structure or design of the code.

`docs/`: When making changes to documentation, like updating README files or adding comments to code.

`chore/`: Routine tasks that need to be done, such as updating dependencies.

`ui/`: Specifically for UI and design changes, like updating layouts, colors, and other visual elements.
- `ui/admin-pages`: for the generator pages in `core/`
- `ui/public-pages`: for the generated pages in `core/app`

> Bear in mind that the project is still running [Boostrap **4**](https://getbootstrap.com/docs/4.0/getting-started/introduction/)!

`content/`: For updates focused on content changes, like text, images, or other media.









# 3. Automated tests

_This project implements two suites of Behat tests: Admin (for the generator) and Public (for the generated pages)._

- Kindly read the tests documentation in [tests/README.md](tests/README.md)
- Install the Composer dependencies and run both test suites
- Make sure that you didn't break anything with your changes
- Edit the tests accordingly
- Create new tests accordingly
- When everything is green, perform the actions in "What is tested now, and how to update the coverage list?" so that everybody knows the code coverage.









# 4. Avoiding conflicts

To ensure that your pull request (PR) for the new feature will not have conflicts with the main branch of the project, follow these steps:

## 4.1 Update Your Local Master Branch

First, make sure your forked local master branch is up to date with the upstream repository (this repo).

```
git checkout master
Switched to branch 'master'
Your branch is ahead of 'origin/master' by 13 commits.
  (use "git push" to publish your local commits)
```

```
git pull upstream master  # Replace 'upstream' with the name of the remote for the original project
From https://github.com/jan-vandenberg/cruddiy
 * branch            master     -> FETCH_HEAD
Already up to date.
```

## 4.2 Rebase Your Feature Branch
Then, rebase your feature branch (`fix/xxx`, `tests`, `feature/yyy`, `ui/public`...) onto your updated master branch. This will apply your changes on top of the latest changes from the original project.

```
git checkout tests
Switched to branch 'tests'  # replace tests with the name of the branch you've worked on
Your branch is up to date with 'origin/tests'.
```

```
git rebase master
Current branch tests is up to date.
```

If there are any conflicts, Git will pause the rebase and allow you to resolve them. Once you've resolved all conflicts, continue the rebase with `git rebase --continue`. Repeat this process until the rebase is complete.


## 4.3 Run Tests and Check for Issues
After the rebase, run the test suites to ensure your changes don't introduce any issues.









# 5. The pull request (PR)

## 5.1 Push Your Changes to Your Fork

```
git push origin tests
```

## 5.2 Create the Pull Request

Go to your fork on GitHub, and create a new pull request from your working branch to the master branch of the original repository.


## 5.3 Check for Conflicts (again) in the Pull Request

Once the PR is created, GitHub will show if there are any merge conflicts. If there are conflicts, it means that there have been changes in the master branch of the original repository that conflict with your changes.

## 5.4 Resolve Any Conflicts

If there are conflicts in the PR, you may need to pull the latest changes from the original repository again, rebase your branch, resolve conflicts, and force push again.

## 5.5 Wait for Review
Once your PR has no conflicts, it's ready for review by the project maintainers.
@jan-vandenberg is the official maintainer (and creator).









# 5. Compatibility stack

- PHP 8+
- MySQL 5.7 or MariaDB 10.3
  - The Behat Admin test suite will create a basic database structure for a small project.
  - In `schema/Example - All field types.sql` we have a definition for all field types
  - We still have issues with some field types (see #75)
- You can use [feature/import schema](#70) to import it and have a consistent set accross tests. The Admin test suite is actually importing one of the schemas.




