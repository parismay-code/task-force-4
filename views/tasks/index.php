<?php

/** @var yii\web\View $this */

/** @var Task[] $modelsList */

require_once Yii::$app->basePath . '/helpers/mainHelper.php';

use app\models\Task;
use yii\helpers\Html;

$this->title = 'Task Force | New Tasks';
?>

<main class="main-content container">
    <div class="left-column">
        <h3 class="head-main head-task">Новые задания</h3>
        <?php foreach ($modelsList as $model): ?>
            <div class="task-card">
                <div class="header-task">
                    <?= Html::a(htmlspecialchars($model->title), ['tasks/view', 'id' => $model->id], ['class' => 'link link--block link--big']) ?>
                    <p class="price price--task"><?= htmlspecialchars($model->budget) ?> ₽</p>
                </div>
                <p class="info-text">
                    <span class="current-time"><?= normalizeDate($model->creation_date) ?> </span>назад
                </p>
                <p class="task-text">
                    <?= htmlspecialchars($model->details) ?>
                </p>
                <div class="footer-task">
                    <p class="info-text town-text">
                        <?= htmlspecialchars($model->city->name) ?>
                    </p>
                    <p class="info-text category-text">
                        <?= htmlspecialchars($model->category->name) ?>
                    </p>
                    <?= Html::a('Смотреть Задание', ['tasks/view', 'id' => $model->id], ['class' => 'button button--black']) ?>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="pagination-wrapper">
            <ul class="pagination-list">
                <li class="pagination-item mark">
                    <a href="#" class="link link--page"></a>
                </li>
                <li class="pagination-item">
                    <a href="#" class="link link--page">1</a>
                </li>
                <li class="pagination-item pagination-item--active">
                    <a href="#" class="link link--page">2</a>
                </li>
                <li class="pagination-item">
                    <a href="#" class="link link--page">3</a>
                </li>
                <li class="pagination-item mark">
                    <a href="#" class="link link--page"></a>
                </li>
            </ul>
        </div>
    </div>
    <div class="right-column">
        <div class="right-card black">
            <div class="search-form">
                <form>
                    <h4 class="head-card">Категории</h4>
                    <div class="form-group">
                        <div class="checkbox-wrapper">
                            <label class="control-label" for="сourier-services">
                                <input type="checkbox" id="сourier-services" checked>
                                Курьерские услуги</label>
                            <label class="control-label" for="cargo-transportation">
                                <input id="cargo-transportation" type="checkbox">
                                Грузоперевозки</label>
                            <label class="control-label" for="translations">
                                <input id="translations" type="checkbox">
                                Переводы</label>
                        </div>
                    </div>
                    <h4 class="head-card">Дополнительно</h4>
                    <div class="form-group">
                        <label class="control-label" for="without-performer">
                            <input id="without-performer" type="checkbox" checked>
                            Без исполнителя</label>
                    </div>
                    <h4 class="head-card">Период</h4>
                    <div class="form-group">
                        <label for="period-value"></label>
                        <select id="period-value">
                            <option>1 час</option>
                            <option>12 часов</option>
                            <option>24 часа</option>
                        </select>
                    </div>
                    <input type="submit" class="button button--blue" value="Искать">
                </form>
            </div>
        </div>
    </div>
</main>