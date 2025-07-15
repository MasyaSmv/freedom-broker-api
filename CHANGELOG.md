# Changelog

Все заметные изменения в этом проекте будут документироваться в этом файле.

Формат основан на [Keep a Changelog](https://keepachangelog.com/ru/1.0.0/) и [Semantic Versioning](https://semver.org/lang/ru/).

## [1.1.0] — 2025-07-11
### Добавлено
- Новый метод `toDbArray()` для `BalanceDTO`, `CommissionDTO`, `OperationDTO`
- Добавлены под этот метод тесты
- Добавлен тест парсера сравнивающий количество записей в отчете брокера с количеством DTO классов

## [1.2.0] - 2025-07-15
### Added
- `ReportPeriodDTO` — объект, описывающий период отчёта брокера.
- Геттеры `start()` и `end()` для `ReportPeriodDTO`.
- Метод `lengthInDays()` и `contains()` для удобной работы с периодом.
- Безопасный парсинг дат в `ReportParser`, даже если данные отсутствуют.

### Fixed
- Исправлен падение при попытке распарсить строку в поле `report.account_at_end.account.positions_from_ts.ps.acc`.
- Исправлен баг в `ReportServiceTest`, когда `report` был строкой вместо массива.


## [Unreleased]
### Fixed
- Исправлен тест `ReportPeriodDTO`. Без приставки test не учитывался в тестировании
-

