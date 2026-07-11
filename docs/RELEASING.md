# Releasing

Only the maintainer creates releases.

1. Create and merge a release-preparation pull request that updates `CHANGELOG.md`, documentation, Composer metadata, and all affected contracts.
2. Confirm `master` is clean and its CI run is successful.
3. In a clean checkout, run:

   ```bash
   composer validate --strict
   composer test
   composer analyse
   composer rector:test
   composer format:test
   ```

4. Create an annotated SemVer tag named `vX.Y.Z` on the verified `master` commit and push it.
5. Wait for CI on that tag to succeed, then create the GitHub Release from the tag using the matching `CHANGELOG.md` section.
6. Publish `trungdev05/laravel-backup-panel` on Packagist and configure its GitHub webhook so future tags are synchronized automatically.

Do not modify or replace a published tag.
