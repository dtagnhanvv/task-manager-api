<?php

namespace Biddy\Bundle\UserSystem\AdminBundle;

final class BiddyUserSystemAdminEvents
{
    const CHANGE_PASSWORD_INITIALIZE = 'biddy_user_system_admin.change_password.edit.initialize';
    const CHANGE_PASSWORD_SUCCESS = 'biddy_user_system_admin.change_password.edit.success';
    const CHANGE_PASSWORD_COMPLETED = 'biddy_user_system_admin.change_password.edit.completed';
    const GROUP_CREATE_INITIALIZE = 'biddy_user_system_admin.group.create.initialize';
    const GROUP_CREATE_SUCCESS = 'biddy_user_system_admin.group.create.success';
    const GROUP_CREATE_COMPLETED = 'biddy_user_system_admin.group.create.completed';
    const GROUP_DELETE_COMPLETED = 'biddy_user_system_admin.group.delete.completed';
    const GROUP_EDIT_INITIALIZE = 'biddy_user_system_admin.group.edit.initialize';
    const GROUP_EDIT_SUCCESS = 'biddy_user_system_admin.group.edit.success';
    const GROUP_EDIT_COMPLETED = 'biddy_user_system_admin.group.edit.completed';
    const PROFILE_EDIT_INITIALIZE = 'biddy_user_system_admin.profile.edit.initialize';
    const PROFILE_EDIT_SUCCESS = 'biddy_user_system_admin.profile.edit.success';
    const PROFILE_EDIT_COMPLETED = 'biddy_user_system_admin.profile.edit.completed';
    const REGISTRATION_INITIALIZE = 'biddy_user_system_admin.registration.initialize';
    const REGISTRATION_SUCCESS = 'biddy_user_system_admin.registration.success';
    const REGISTRATION_COMPLETED = 'biddy_user_system_admin.registration.completed';
    const REGISTRATION_CONFIRM = 'biddy_user_system_admin.registration.confirm';
    const REGISTRATION_CONFIRMED = 'biddy_user_system_admin.registration.confirmed';
    const RESETTING_RESET_INITIALIZE = 'biddy_user_system_admin.resetting.reset.initialize';
    const RESETTING_RESET_SUCCESS = 'biddy_user_system_admin.resetting.reset.success';
    const RESETTING_RESET_COMPLETED = 'biddy_user_system_admin.resetting.reset.completed';
    const SECURITY_IMPLICIT_LOGIN = 'biddy_user_system_admin.security.implicit_login';
}
