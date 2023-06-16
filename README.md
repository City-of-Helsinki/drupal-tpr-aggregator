# Drupal TPR aggregator

Aggregates TPR data to make it easier to be processed by [drupal-module-helfi-tpr](https://github.com/City-of-Helsinki/drupal-module-helfi-tpr) migrations.

See [gh-pages](https://github.com/City-of-Helsinki/drupal-tpr-aggregator/tree/gh-pages) branch for available dumps.

## Testing locally

Clone the repository and run `composer install`.

Run:

```bash
php console.php app:aggregate-tpr-services description > services.json
php console.php app:aggregate-tpr-services errandservice > errandservices.json
php console.php app:aggregate-unit-services services.json > unitservices.json
```
