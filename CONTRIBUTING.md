# Contributing

Issues and pull requests are welcome. Search existing work before opening either one, and use the matching GitHub form. Do not report security vulnerabilities in public issues; follow the [security policy](SECURITY.md).

## Pull requests

- Create one focused branch and pull request per change.
- Describe the user-visible behavior, tests, and documentation changes in the pull request template.
- Add focused regression coverage for behavioral changes.
- Run the required checks locally:

  ```bash
  composer test
  composer analyse
  composer rector:test
  composer format:test
  ```

- Read [`docs/DATA_CONTRACT_INTEGRITY_RULE.md`](docs/DATA_CONTRACT_INTEGRITY_RULE.md) before changing requests, configuration, queue payloads, view data, public APIs, tests, or documentation. Contract changes in the pre-release 3.x line update every in-repository producer and consumer together, with no compatibility aliases.
- Keep documentation current when behavior or public contracts change. Maintainers own the release changelog entry.

## Review and release

`master` accepts squash-merged pull requests with passing required checks. The maintainer decides scope and merge timing. Releases follow [SemVer](https://semver.org/) and the maintainer-only [release procedure](docs/RELEASING.md).
