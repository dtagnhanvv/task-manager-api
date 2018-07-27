<?php

namespace Biddy\Bundle\UserSystem\SaleBundle;

final class BiddyUserSystemSaleEvents
{
    const CHANGE_PASSWORD_INITIALIZE = 'biddy_user_system_sale.change_password.edit.initialize';
    const CHANGE_PASSWORD_SUCCESS = 'biddy_user_system_sale.change_password.edit.success';
    const CHANGE_PASSWORD_COMPLETED = 'biddy_user_system_sale.change_password.edit.completed';
    const GROUP_CREATE_INITIALIZE = 'biddy_user_system_sale.group.create.initialize';
    const GROUP_CREATE_SUCCESS = 'biddy_user_system_sale.group.create.success';
    const GROUP_CREATE_COMPLETED = 'biddy_user_system_sale.group.create.completed';
    const GROUP_DELETE_COMPLETED = 'biddy_user_system_sale.group.delete.completed';
    const GROUP_EDIT_INITIALIZE = 'biddy_user_system_sale.group.edit.initialize';
    const GROUP_EDIT_SUCCESS = 'biddy_user_system_sale.group.edit.success';
    const GROUP_EDIT_COMPLETED = 'biddy_user_system_sale.group.edit.completed';
    const PROFILE_EDIT_INITIALIZE = 'biddy_user_system_sale.profile.edit.initialize';
    const PROFILE_EDIT_SUCCESS = 'biddy_user_system_sale.profile.edit.success';
    const PROFILE_EDIT_COMPLETED = 'biddy_user_system_sale.profile.edit.completed';
    const REGISTRATION_INITIALIZE = 'biddy_user_system_sale.registration.initialize';
    const REGISTRATION_SUCCESS = 'biddy_user_system_sale.registration.success';
    const REGISTRATION_COMPLETED = 'biddy_user_system_sale.registration.completed';
    const REGISTRATION_CONFIRM = 'biddy_user_system_sale.registration.confirm';
    const REGISTRATION_CONFIRMED = 'biddy_user_system_sale.registration.confirmed';
    const RESETTING_RESET_INITIALIZE = 'biddy_user_system_sale.resetting.reset.initialize';
    const RESETTING_RESET_SUCCESS = 'biddy_user_system_sale.resetting.reset.success';
    const RESETTING_RESET_COMPLETED = 'biddy_user_system_sale.resetting.reset.completed';
    const SECURITY_IMPLICIT_LOGIN = 'biddy_user_system_sale.security.implicit_login';
}