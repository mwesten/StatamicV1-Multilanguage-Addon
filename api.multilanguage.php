<?php

class API_multilanguage extends API
{

    /**
     * Get current language code
     *
     * @return array
     */
    public function getCurrentLanguage()
    {
        return $this->tasks->currlang();
    }


}