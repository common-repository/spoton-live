<?php

require_once(dirname(__FILE__) . '/../AbstractModule.php');

class Module_GravityForms extends AbstractModule
{
    const WEBHOOK_URL = 'webhooks/gravity-forms';

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
        $this->on('gform_after_submission', function ($entry, $form) {
            $this->afterSubmission($entry, $form);
        }, 10, 2);
    }

    /**
     * After submission
     *
     * @param array $submission
     * @param array $form
     */
    public function afterSubmission(array $submission, array $form)
    {
        $formId = (int) $form['id'];

        if ($this->isExcluded($formId)) {
            return;
        }

        $submissionValues = [];

        foreach ($form['fields'] as $field) {
            if (!isset($submission[$field->id]) || empty($submission[$field->id])) {
                continue;
            }

            $submissionValues[] = [
                'key' => $field->label,
                'value' => $submission[$field->id],
                'type' => $field->type,
            ];
        }

        // Build request

        $body = array_merge([
            'apiKey' => $this->getOption('key'),

            // Form
            'formId' => $formId,
            'formTitle' => (string) $form['title'],

            // Submission
            'submissionId' => (string) $submission['id'],
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
