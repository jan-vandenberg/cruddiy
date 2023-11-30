# 1. General

- Fork the project
- Create a branch from `master` for your fix/feature/etc preferably following Git Flow naming
- When done, commit and push to your branch
- Create a pull request (PR) explaining what you did, and/or referencing the issue #id you have worked in


# 2. Git Flow

Altough there is no requirement, it is considered a good practice to use Git Flow branch names when contributing to open source projects. **Apart from `feature` and `fix` branches, here are some other meaningful branch names that are commonly used:**

`hotfix/`: Used for making quick fixes to the production version. These are similar to fix/ branches but are usually more urgent and bypass the usual development and staging process.

`release/`: Branches off from develop when the codebase reaches a point where it's ready for release. It’s used for final polishing and bug fixing before the code is merged into master and tagged with a version number.

`develop`: Use locally. This is a main branch where all the development happens. Feature and fix branches usually branch off from here.

`master`: This is the source where to you to create your PR against.



**For small evolutions, wording/image changes, and other minor updates, you might consider the following branch naming conventions:**

`enhancement/` or `improvement/`: For small but meaningful changes that enhance features without adding completely new functionality.

`refactor/`: For code refactoring that doesn’t necessarily add new features or fix bugs but improves the structure or design of the code.

`docs/`: When making changes to documentation, like updating README files or adding comments to code.

`style/`: For changes that do not affect the meaning of the code (white-space, formatting, missing semi-colons, etc).

`chore/`: Routine tasks that need to be done, such as updating dependencies.

`ui/` or `design/`: Specifically for UI and design changes, like updating layouts, colors, and other visual elements.
- More specifically, the branches `ui/public-pages` and `ui/admin-pages` are inteded for the generated pages, and the generator pages, respectively
- Bear in mind that the project is still running [Boostrap **4**](https://getbootstrap.com/docs/4.0/getting-started/introduction/)

`content/`: For updates focused on content changes, like text, images, or other media.


# 3. Tests

_This project implements two suites of Behat tests: Admin (for the generator) and Public (for the generated pages).

- Kindly read the tests documentation in `tests/README.md`
- Install the Composer dependencies and run both test suites
- Make sure that you didn't break anything with your changes
- Edit the tests accordingly
- Create new tests accordingly
- When everything is green, perform the actions in "What is tested now, and how to update the coverage list?" so that everybody knows the code coverage.


# 4. Wait for approval

@jan-vandenberg is the official maintainer (and creator) and will have the power to accept or refuse your PR.


# 5. Stack

- The project was designed to work with PHP 7.4 but PHP 8+ is recommended
- You can use [schema/Example - All field types.sql](schema/Example - All field types.sql) to create a sample table. We still have issues with some field types (see #75)
- You can use [feature/import schema](#70) to import it and have consistent sent accross tests
- Unfortunately we don't have unit tests or functional tests at the moment, that'd be a great addition!




