<?php
// Version: 1.0.1; ManageAds

// Ads
$txt['sa_ads_name'] = 'Название рекламного объявления';
$txt['sa_ads_body_template'] = 'Содержание шаблона';
$txt['sa_ads_body'] = 'Содержание объявления';
$txt['sa_ads_status'] = 'Статус';

$txt['sa_ads_click_limit'] = 'Ограничение кликов';
$txt['sa_ads_impression_limit'] = 'Ограничение показов';
$txt['sa_ads_expiration'] = 'Окончание срока';
$txt['sa_ads_created'] = 'Создан';

$txt['sa_ads_positions'] = 'Расположение';
$txt['sa_ads_membergroups'] = 'Группы участников';
$txt['sa_ads_actions'] = 'Область размещения объявления';
$txt['sa_ads_boards'] = 'Разделы форума';
$txt['sa_ads_custom_display'] = 'Произвольное отображение';

$txt['sa_ads_clicks'] = 'Клики';
$txt['sa_ads_impressions'] = 'Показы';
$txt['sa_ads_expires'] = 'Заканчивается';

$txt['sa_no_ads'] = 'Нет созданных рекламных объявлений.';

// Positions
$txt['sa_positions_name'] = 'Название области для размещения';
$txt['sa_positions_namespace'] = 'Область для размещения объявления';
$txt['sa_positions_type'] = 'Тип';

$txt['sa_positions_notice_title'] = 'Расположение';
$txt['sa_positions_notice_body'] = '<p>Position успешно создана!</p><p>Чтобы отобразить рекламное объявление для созданной позиции, пожалуйста, добавьте следующий вызов функции в то место, где вы хотите, чтобы реклама отображалась:<br /><br /><code class="bbc_code">template_ad_position(\'%1$s\');</code></p>';
$txt['sa_no_positions'] = 'Там нет никаких созданных позиций.';

// Generic
$txt['sa_generic_actions'] = 'Выбрать';
$txt['sa_generic_continue'] = 'Продолжать';
$txt['sa_generic_edit'] = 'Редактировать';
$txt['sa_generic_delete'] = 'Удалить';

$txt['sa_generic_active'] = 'Активный';
$txt['sa_generic_disabled'] = 'Выключен';

$txt['sa_generic_expired'] = 'Закончился';
$txt['sa_generic_never'] = 'Никогда';

$txt['sa_generic_membergroup'] = 'Группа участников';
$txt['sa_generic_groups_allow'] = 'A';
$txt['sa_generic_groups_disallow'] = 'X';
$txt['sa_generic_groups_deny'] = 'D';

$txt['sa_generic_expire_days'] = 'День';
$txt['sa_generic_expire_months'] = 'Месяц';
$txt['sa_generic_expire_years'] = 'Год';

$txt['sa_generic_board_index'] = 'Board Index';

$txt['sa_generic_plain'] = 'Простой';
$txt['sa_generic_rotating'] = 'Ротатор';

// Errors
$txt['sa_error_empty_name'] = 'Вы оставили поле Название пустым.';
$txt['sa_error_empty_body'] = 'Вы оставили поле для рекламного объявления пустым.';
$txt['sa_error_empty_namespace'] = 'Вы оставили поле Область для размещения пустым.';
$txt['sa_error_duplicate_namespace'] = 'Выбранная область используется. Область должна быть уникальной.';
$txt['sa_error_invalid_namespace'] = 'Название области может содержать только буквы, цифры и символы подчеркивания.';

// Admin
$txt['sa_admin_cat_title'] = 'SimpleAds';
$txt['sa_admin_title'] = 'Рекламные объявления';
$txt['sa_admin_desc'] = 'Вы можете управлять рекламой и позициями в этой области.';
$txt['sa_ads_list_title'] = 'Список';
$txt['sa_ads_add_title'] = 'Добавить блок';
$txt['sa_ads_edit_title'] = 'Редактировать блок';
$txt['sa_positions_list_title'] = 'Позиция списка';
$txt['sa_positions_add_title'] = 'Добавить позицию';
$txt['sa_positions_edit_title'] = 'Редактировать позицию';

$helptxt['sa_ads_custom_display'] = 'Это поле используется для определения конкретного действия (действий), на котором вы хотите отобразить объявление, т. е. {action} == \'contact\' || {action} == \'forum\'.  Можно также ввести все, чтобы включить везде. Используйте это, если действие не является одним из стандартных, определенных выше';