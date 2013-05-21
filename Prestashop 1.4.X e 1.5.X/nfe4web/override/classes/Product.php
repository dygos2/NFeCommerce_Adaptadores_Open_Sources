<?php
/*
 * Adaptado por PrestaBR <http://prestabr.com.br>
 *
 */
 
class Product extends ProductCore
{
    /** @var string ncm */
    public $ncm;

    /** @var integer subst */
    public $subst;
    
    public function getFields()
    {
        $fields = parent::getFields();
        $fields['ncm'] = pSQL($this->ncm);
        $fields['subst'] = pSQL($this->subst);
        return $fields;
    }
}
