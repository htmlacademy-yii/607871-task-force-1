
<section class="user__search">

    <?php foreach ($users as $user): ?>
    <div class="content-view__feedback-card user__search-wrapper">
        <div class="feedback-card__top">
            <div class="user__search-icon">
                <a href="user.html"><img src="./img/man-glasses.jpg" width="65" height="65"></a>
                <span>17 заданий</span>
                <span>6 отзывов</span>
            </div>
            <div class="feedback-card__top--name user__search-card">
                <p class="link-name"><a href="user.html" class="link-regular"><?= $user->name; ?></a></p>
                <span></span><span></span><span></span><span></span><span class="star-disabled"></span>
                <b>4.25</b>
                <p class="user__search-content">
                    <?= $user->profile->description; ?>
                </p>
            </div>
            <span class="new-task__time">Был на сайте <?=$user->getRelativeTime($user->last_visit_date); ?></span>
        </div>
        <div class="link-specialization user__search-link--bottom">

            <?php foreach ($user->categories as $category): ?>
            <a href="browse.html" class="link-regular"><?= $category->name; ?></a>

            <?php endforeach; ?>
        </div>
    </div>
   <?php endforeach; ?>
</section>
<section class="search-task">
    <div class="search-task__wrapper">
        <form class="search-task__form" name="users" method="post" action="#">
            <fieldset class="search-task__categories">
                <legend>Категории</legend>
                <label class="checkbox__legend">
                    <input class="visually-hidden checkbox__input" type="checkbox" name="" value="" checked disabled>
                    <span>Курьерские услуги</span>
                </label>
                <label class="checkbox__legend">
                    <input class="visually-hidden checkbox__input" type="checkbox" name="" value="" checked>
                    <span>Грузоперевозки</span>
                </label>
                <label class="checkbox__legend">
                    <input class="visually-hidden checkbox__input" type="checkbox" name="" value="">
                    <span>Переводы</span>
                </label>
                <label class="checkbox__legend">
                    <input class="visually-hidden checkbox__input" type="checkbox" name="" value="">
                    <span>Строительство и ремонт</span>
                </label>
                <label class="checkbox__legend">
                    <input class="visually-hidden checkbox__input" type="checkbox" name="" value="">
                    <span>Выгул животных</span>
                </label>
            </fieldset>
            <fieldset class="search-task__categories">
                <legend>Дополнительно</legend>
                <label class="checkbox__legend">
                    <input class="visually-hidden checkbox__input" type="checkbox" name="" value="">
                    <span>Сейчас свободен</span>
                </label>
                <label class="checkbox__legend">
                    <input class="visually-hidden checkbox__input" type="checkbox" name="" value="">
                    <span>Сейчас онлайн</span>
                </label>
                <label class="checkbox__legend">
                    <input class="visually-hidden checkbox__input" type="checkbox" name="" value="">
                    <span>Есть отзывы</span>
                </label>
                <label class="checkbox__legend">
                    <input class="visually-hidden checkbox__input" type="checkbox" name="" value="">
                    <span>В избранном</span>
                </label>
            </fieldset>
            <label class="search-task__name" for="110">Поиск по имени</label>
            <input class="input-middle input" id="110" type="search" name="q" placeholder="">
            <button class="button" type="submit">Искать</button>
        </form>
    </div>
</section>