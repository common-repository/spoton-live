<?php

require_once(dirname(__FILE__) . '/../AbstractModule.php');

class Module_ContactForm7 extends AbstractModule
{
    const WEBHOOK_URL = 'webhooks/contact-form-7';

    /**
     * Attach module
     */
    public function attach()
    {
        if (!$this->isActive('forms')) {
            return;
        }

        $this->bindListeners();
    }

    /**
     * Bind listeners
     */
    public function bindListeners()
    {
        $this->on('wpcf7_mail_sent', function ($form) {
            $this->afterSubmission($form);
        }, 10, 2);
    }

    /**
     * After submission
     *
     * @param WPCF7_ContactForm $form
     */
    public function afterSubmission($form)
    {
        if (!class_exists('WPCF7_ContactForm') ||
            !class_exists('WPCF7_Submission')
        ) {
            return;
        }
        $formId = (int) $form->id();

        if ($this->isExcluded($formId)) {
            return;
        }

        $submission = WPCF7_Submission::get_instance();
        $submissionValues = [];

        foreach ($form->scan_form_tags() as $field) {
            if (empty($field->name)) {
                continue;
            }

            $value = $submission->get_posted_data($field->name);

            if (empty($value)) {
                continue;
            }

            switch ($field->basetype) {
                case 'file':
                    $files = $submission->uploaded_files();
                    $uploadedPath = $files[$field->name];

                    // Filename
                    $fileName = uniqid() . '_' . basename($uploadedPath);

                    // Upload
                    $uploadDirectory = wp_upload_dir();

                    copy($uploadedPath, $uploadDirectory['path'] . '/' . $fileName);

                    // External path

                    $value = $uploadDirectory['url'] . '/' . $fileName;
                    break;
            }

            $submissionValues[] = [
                'key' => $field->name,
                'value' => $value,
                'type' => $field->basetype,
            ];
        }

        // Build request

        $body = array_merge([
            'apiKey' => $this->getOption('key'),

            // Form
            'formId' => $formId,
            'formTitle' => (string) $form->name(),

            // Submission
            'submissionId' => time(),
            'submissionFields' => $submissionValues,
        ], $this->utm());

        // Post to webhook

        wp_remote_post(parent::API_URL . self::WEBHOOK_URL, [
            'method' => 'POST',
            'timeout' => 45,
            'headers' => [],
            'body' => $body,
        ]);
    }

    /**
     * UTM options
     *
     * @return array
     */
    public function utm()
    {
        $values = $_COOKIE;
        $utmFields = ['utm_source', 'utm_medium', 'utm_term', 'last_referrer'];

        $utmValues = [];

        foreach ($utmFields as $utmField) {
            $key = sprintf('_uc_%s', $utmField);

            if (!isset($values[$key])) {
                continue;
            }

            $utmValues[$utmField] = $values[$key];
        }

        return $utmValues;
    }

    /**
     * Check if form is excluded
     *
     * @param string $id
     * @return bool
     */
    public function isExcluded($id)
    {
        return in_array($id, $this->excludedForms());
    }

    /**
     * Excluded forms
     *
     * @return array
     */
    public function excludedForms()
    {
        $ids = $this->getOption('exclude_forms', null);

        if (empty($ids)) {
            return [];
        }

        return array_map(
            'trim',
            explode(',', $ids)
        );
    }
}
