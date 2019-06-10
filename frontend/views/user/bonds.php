<main class="c-content d-flex flex-column">
    <div class="c-content__wrapper">
        <div class="u-page__head">
            <div class="container">
                <div class="b-h2">Индивидуальная стратегия</div>
                <div class="back-list--item">
                    <div class="starting-from">Старт от <span>5 000<span>$</span></span></div>
                </div>
            </div>
        </div>
        <div class="u-page__holder-bg" style="background-image: url(/img/bonds-header-bg.png)"></div>
        <div class="c-header-form">
            <div class="container">
                <div class="c-header-form__top">
                    <form class="form form-centered-wide">
                        <div class="form-group__wrapper">
                            <div class="form-group">
                                <div class="label__container">
                                    <label for="invest">Инвестировать</label>
                                </div>
                                <div class="currency__wrapper">
                                    <input class="form-control" id="invest" type="text" placeholder="25000"><span class="icon-currency">$</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="term">На срок</label>
                                <select class="form-control" id="term">
                                    <option value="18">24 недели</option>
                                    <option value="19">42 недели</option>
                                    <option value="20">77 недель</option>
                                </select>
                            </div>
                        </div>
                        <div class="u-btn__container">
                            <button class="u-btn u-btn-action" type="submit"><span>Инвестировать</span></button>
                        </div>
                    </form>
                </div>
                
            </div>
        </div>
        <div class="c-card">
            <div class="container">
                <h4 class="b-h4">Приятный сюрприз от invest</h4>
                <div class="c-card__container">
                    <div class="row">
                        <div class="col">
                            <div class="c-card__item"><span class="digit">5 %</span>
                                <div class="text text-center">от прибыли в подарок от<b> 20</b>-ти недель инвестирования</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="c-card__item"><span class="digit">10 %</span>
                                <div class="text text-center">от прибыли в подарок от<b> 30</b>-ти недель инвестирования</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="c-card__item"><span class="digit">15 %</span>
                                <div class="text text-center">от прибыли в подарок от<b> 45</b>-ти недель инвестирования</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <?php
    $this->beginContent('@app/views/layouts/_footer.php');
    $this->endContent();
    ?>
</main>