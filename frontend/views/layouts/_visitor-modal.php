<?php
use common\models\VisitorLog;

?>

<div class="modal fade modal-meeting-form" id="meeting-form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog meeting-form">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal__title">Запись на бизнес встречу</h4>
                <div class="step-one"  >
                    <h6>Поля со звездой обязательны для заполнения</h6>
                    <form id="meeting_form_step_1" action="#">
                        <div class="form-group">
                            <label for="#">Выберите город <span>*</span></label>
                            <div class="u-select-section">
                                <select name="visit_city" class="u-input-style u-select-init" >
                                    <?php
                                    foreach (VisitorLog::cities as $key => $value) { ?>
                                        <option value="<?= $key ?>"><?= $value ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="#">Дата и время <span>*</span></label>
                            <input type="date" name="visit_date" required value="<?= date('d.m.Y', strtotime(' +1 day')) ?>" min="<?= date('Y-m-d', strtotime(' +1 day')) ?>"class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="#">Ваше имя <span>*</span></label>
                            <input type="text" name="visit_name" required value="" placeholder="Имя" class="form-control">
                        </div>
                        <div class="form-group form_phone">
                            <label for="#">Номер телефона <span>*</span></label>
                            <input type="text" name="visit_phone" id="meeting_form_step_1_phone" value="" required placeholder="Телефон" class="form-control">
                            <div class="help-error" style="display: none">Некорректные данные</div>
                        </div>
                        <div class="form-group__btn">
                            <button type="submit" class="c-btn is-bg-color-black u-btn-benefit-start-now">Подтвердить</button>
                        </div>
                    </form>
                </div>
                <div class="step-two" style="display:none;">
                    <h6>Мы выслали СМС на Ваш номер <a href="#" id="meeting_form_step_2_another">Указать другой номер</a></h6>
                    <form id="meeting_form_step_2" action="#" data-phone="">
                        <div class="form-group form_phone">
                            <label for="#">Код из СМС</label>
                            <input type="text" id='meeting_form_step_2_code' required value="" placeholder="0679930" class="form-control">
                            <div class="help-error" style="display: none">Неверный код</div>
                        </div>
                        <div class="form-group__btn">
                            <button type="submit" class="c-btn is-bg-color-black u-btn-benefit-start-now">отправить заявку</button>
                        </div>
                    </form>
                </div>
                <div class="step-three" style="display:none;">
                    <div class="success-step">Спасибо! Ожидайте, в скором времени персональный менеджер invest24 с вами свяжется</div>
                </div>
            </div>
        </div>
    </div>
</div>

