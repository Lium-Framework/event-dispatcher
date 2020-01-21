# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

<!--

## [Unreleased]

### Added

### Changed

### Removed

-->

## [Unreleased]

### Added

- Add `Lium\EventDispatcher\ListenerProvider\DefaultListenerProviderBehavior` trait
- Add `Lium\EventDispatcher\ListenerProvider\ResettableListenerProvider` interface
- PHP Insight integration

### Changed

- Rename `Lium\EventDispatcher\Exception\InvalidListenerException` to `Lium\EventDispatcher\Exception\InvalidListener`
- Rename `Lium\EventDispatcher\StoppableEventTrait` to `Lium\EventDispatcher\StoppableEventBehavior`
- Rename `Lium\EventDispatcher\Test\StoppableEventTraitTest` to `Lium\EventDispatcher\Test\StoppableEventBehaviorTest`

### Removed

- Remove `./examples` folder

## [0.0.1] - 2019-12-08

Initial release

[unreleased]: https://github.com/Lium-Framework/event-dispatcher/compare/v0.1.0...master
[0.0.1]: https://github.com/Lium-Framework/event-dispatcher/releases/tag/v0.1.0
