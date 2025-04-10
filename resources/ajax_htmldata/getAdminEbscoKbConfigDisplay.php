<h3 class="adminRightHeader"><?php echo _("EBSCO Knowledge Base Configuration");?></h3>
<form id="ebscoKbConfig">
    <div id="ebscoKbConfigError" class="error"></div>
    <div class="block-form">
        <p class="checkbox">
        <label for="ebscoKbEnabled">
            <input type="checkbox" name="enabled" id="ebscoKbEnabled" value="true" <?php echo $config->settings->ebscoKbEnabled == 'Y' ? 'checked' : ''; ?>>
            <?php echo _("Enable EBSCO Knowledge Base"); ?>
        </label>
        </p>
        <div class="flex">
            <p>
            <label for="ebscoKbCustomerId" style="display: block;"><?php echo _('Customer ID'); ?></label>
            <input type="text" name="customerId" value="<?php echo $config->settings->ebscoKbCustomerId; ?>" id="ebscoKbCustomerId">
        </p>
        <p>
            <label for="ebscoKbApiKey" style="display: block;"><?php echo _('API Key'); ?></label>
            <input type="text" name="apiKey" value="<?php echo $config->settings->ebscoKbApiKey; ?>" id="ebscoKbApiKey" style="width: 100%;">
        </p>
</div>
    <p>
        <button class="primary" type="submit" ><?php echo _('Save'); ?></button>
    </p>
    </div>
</form>

