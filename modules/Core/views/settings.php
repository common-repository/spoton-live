<?php
$core = new Module_core();

$hooks = $core->isActive('hooks');
$forms = $core->isActive('forms');

$key = esc_attr($core->getOption('key'));
$excludeForms = esc_attr($core->getOption('exclude_forms'));

$callTracking = $core->getOption('call_tracking');
?>
<div class="wrap">
    <form method="post" action="options.php?updated=1">
        <?php settings_fields( 'spotonmarketing' ); ?>
        <?php do_settings_sections( 'spotonmarketing' ); ?>

        <div style="padding: 40px 50px; max-width: 600px;">
            <img src="<?=plugin_dir_url(__FILE__) . '../images/Logo-RGB.png'?>" style="max-width: 200px;">

            <div style="margin-top: 40px">
                <p>
                    <?=_('Her kan du ændre på indstillingerne for dit SpotOn Live plugin.')?>
                    <?=_('Herunder aktivere og deaktivere forskellige dele af plugin\'et.')?>
                </p>
            </div>

            <div style="margin-top: 60px">
                <?php if (isset($_GET['settings-updated'])) { ?>
                    <div class="notice notice-success is-dismissible">
                        <p><?php _e( __('Dine ændringer er blevet gemt'), 'spotonmarketing' ); ?></p>
                    </div>
                <?php } ?>

                <table style="border: 0; width: 100%;">
                    <!-- Settings -->

                    <tr>
                        <td >
                            <strong><?=__('API nøgle')?></strong>&nbsp;
                        </td>

                        <td>
                            <input type="text" name="spoton_key" style="width: 100%;" value="<?=$key?>"><br />

                            <div style="float: right; text-align: right;">
                                <small>
                                    <span class="spoton-api-status spoton-api-status-success" style="color: green;">
                                        <?=__('Success')?>
                                    </span>

                                    <span class="spoton-api-status spoton-api-status-error" style="color: red;">
                                        <?=__('Fejl i forbindelsen')?>
                                    </span>

                                    <span class="spoton-api-loading">
                                        <?=__('Skaber forbindelse...')?>
                                    </span>

                                    <a onclick="spoton.api.check()" class="spoton-api-action" style="cursor: pointer;">
                                        <?=__('Test forbindelse')?>
                                    </a>
                                </small>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" style="height: 20px;"></td>
                    </tr>

                    <?php if (!empty($key)) { ?>

                    <tr>
                        <td>
                            <strong><?=_('Emailtracking')?></strong><br />
                            <small><?=_('Understøtter GravityForms & ContactForm7')?></small>
                        </td>

                        <td>
                            <select name="spoton_activate_forms" style="width: 100%;">
                                <option value="1" <?php if ($forms) echo ' selected="selected"'?>><?=__('Aktiveret')?></option>
                                <option value="0" <?php if (!$forms) echo ' selected="selected"'?>><?=__('Deaktiveret')?></option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" style="height: 20px;"></td>
                    </tr>

                    <tr class="call-tracking">
                        <td>
                            <strong><?=__('Opkaldstracking')?></strong><br />
                            <small><?=_('Forbeholdt SpotOn Live Business')?></small>
                        </td>

                        <td>
                            <span class="loading"><?=__('Indlæser...')?></span>

                            <button type="button" onclick="spoton.callTracking.activate()" class="button button-primary button-deactivated">Aktiver</button>
                            <button type="button" onclick="spoton.callTracking.deactivate()" class="button button-default button-activated">Deaktiver</button>

                            <a style="text-decoration: underline" class="navigate-to-spoton-live">Klik her for at opsætte call tracking</a>

                            <a style="text-decoration: underline" class="succeeded">Opsætningen er afsluttet. Klik her for at vælge dine dynamiske numre.</a>
                        </td>
                    </tr>

                    <tr class="call-tracking-spacer">
                        <td colspan="2" style="height: 20px;"></td>
                    </tr>

                    <?php } ?>


                    <!-- Advanced Settings -->

                    <tr>
                        <td>
                            <a onclick="spoton.advancedSettings.show()" class="spoton-advanced-settings-show">Vis avanceret indstillinger</a>
                            <a onclick="spoton.advancedSettings.hide()" class="spoton-advanced-settings-hide hidden">Skjul avanceret indstillinger</a>
                        </td>

                        <td></td>
                    </tr>

                    <tr class="call-tracking-spacer">
                        <td colspan="2" style="height: 20px;"></td>
                    </tr>

                    <tr class="spoton-hooks">
                        <td>
                            <strong><?=_('SpotOn Hooks')?></strong><br />
                            <small><?=_('WordPress updates etc.')?></small>
                        </td>

                        <td>
                            <select name="spoton_activate_hooks" style="width: 100%;">
                                <option value="1" <?php if ($hooks) echo ' selected="selected"'?>><?=__('Aktiveret')?></option>
                                <option value="0" <?php if (!$hooks) echo ' selected="selected"'?>><?=__('Deaktiveret')?></option>
                            </select>
                        </td>
                    </tr>

                    <tr class="spoton-hooks-spacer">
                        <td colspan="2" style="height: 20px;"></td>
                    </tr>

                    <tr class="spoton-exclude-forms">
                        <td>
                            <strong><?=_('Eksluder formularer')?></strong><br />
                            <small><?=_('Ex: 1,2,3')?></small>
                        </td>

                        <td>
                            <input type="text" name="spoton_exclude_forms" style="width: 100%;" value="<?=$excludeForms?>">
                        </td>
                    </tr>

                </table>

                <?php submit_button(); ?>
            </div>
        </div>
    </form>
</div>