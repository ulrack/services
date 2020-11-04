# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 3.2.0 2020-11-04
### Added
- Ability to register `global` hooks in service compilers and factories.

## 3.1.0 2020-10-27
### Added
- `resolveReferences` method to `AbstractServiceFactoryExtension`.
- `PassThroughCompiler` for simple services that do not require any special compiler logic.

## 3.0.4 2020-09-06
### Fixed
- Allow numbers in class declarations.

## 3.0.3 2020-08-30
### Fixed
- Issue with nested references not being resolved in the services factory.

## 3.0.2 2020-08-17
### Fixed
- Preparing parameters caused hook logic to be skipped.

## 3.0.1 2020-08-11
### Fixed
- Not all exceptions being caught by services exceptions.

## 3.0.0 2020-06-08
### Added
- Method reflector as a requirement for the service factory.
- Method reflector as internal service for the service factory.

## 2.0.0 2020-06-03
### Added
- Pre-registration support for all factories.

### Refactored
- The creation of references to other services, so they can have cross-dependencies.

### Fixed
- Deeper references to other objects for the services.

## 1.1.2 - 2020-05-02
### Fixed
- Fix issue for registering previously compiled objects.

## 1.1.1 - 2020-05-02
### Fixed
- Resolving to the correct pre registered service.
- Implemented PSR12 coding standard.

## 1.1.0 - 2020-04-19
### Added
- Added possibility to preload objects in the ServicesFactory.

## 1.0.4 - 2020-04-15
### Fixed
- Fixed issue in the schema's and how these are being resolved.

## 1.0.3 - 2020-04-09
### Changed
- Changed packages to GrizzIT variations.

## 1.0.2 - 2020-03-05
### Changed
- Changed company name references.

## 1.0.1 - 2020-02-19
### Added
- $id to schema's so they can be referenced differently in the application.
- locator.php file and added it to the autoload of composer, to allow discovery of the configuration.

## 1.0.0 - 2020-01-18
### Added
- Created the initial services layer.

- [3.1.0 > Unreleased](https://github.com/ulrack/services/compare/3.1.0...HEAD)
- [3.0.4 > 3.1.0](https://github.com/ulrack/services/compare/3.0.4...3.1.0)
- [3.0.3 > 3.0.4](https://github.com/ulrack/services/compare/3.0.3...3.0.4)
- [3.0.2 > 3.0.3](https://github.com/ulrack/services/compare/3.0.2...3.0.3)
- [3.0.1 > 3.0.2](https://github.com/ulrack/services/compare/3.0.1...3.0.2)
- [3.0.0 > 3.0.1](https://github.com/ulrack/services/compare/3.0.0...3.0.1)
- [2.0.0 > 3.0.0](https://github.com/ulrack/services/compare/2.0.0...3.0.0)
- [1.1.2 > 2.0.0](https://github.com/ulrack/services/compare/1.1.2...2.0.0)
- [1.1.1 > 1.1.2](https://github.com/ulrack/services/compare/1.1.1...1.1.2)
- [1.1.0 > 1.1.1](https://github.com/ulrack/services/compare/1.1.0...1.1.1)
- [1.0.4 > 1.1.0](https://github.com/ulrack/services/compare/1.0.4...1.1.0)
- [1.0.3 > 1.0.4](https://github.com/ulrack/services/compare/1.0.3...1.0.4)
- [1.0.2 > 1.0.3](https://github.com/ulrack/services/compare/1.0.2...1.0.3)
- [1.0.1 > 1.0.2](https://github.com/ulrack/services/compare/1.0.1...1.0.2)
- [1.0.0 > 1.0.1](https://github.com/ulrack/services/compare/1.0.0...1.0.1)