<?php
/*
 * @author  Hédi Carlos Minin
 * @email   hedicarlos@gmail.com
 * 
 * @author  André da Silva Severino
 * @email   andre@andrewd.com.br
 * 
 * @url     http://andrewebdeveloper.github.io/brtalk/
 * 
 * @version 1.0
 */
class Validation {

    private $fields = array();
    private $method = 'post';
    private $errors = array();
    private $messages = array();

    public function __construct(){}

    public function validate(){

        $this->fields = $this->method == 'post' ? $_POST : $_GET;

        $scriptName = explode('/', $_SERVER['SCRIPT_FILENAME']);
        $xmlFile = 'validation/'.str_replace('.php','_validation.xml', $scriptName[(sizeof($scriptName) - 1)]);

        if(file_exists($xmlFile) == false){
            $this->addError('Arquivo de validação não encontrado ('.$xmlFile.')');
            return false;	
        }

        $xml = file_get_contents($xmlFile);
        $xmlObj = simplexml_load_string($xml, NULL, LIBXML_NOERROR | LIBXML_NOWARNING);

        if($xmlObj == false){
            $this->addError('Falha ao executar parse do XML, verifique sua estrutura');
            return false;	
        }

        foreach($xmlObj->children() as $field):

            $attributes = $field->attributes();
            $fieldName = (string) $attributes['name'];

            foreach($field->children() as $validatorType):

                $attributes = $validatorType->attributes();
                $errorMessage = (string) $validatorType;

                switch($validatorType->getName()){
                    case 'required':

                        $fieldValue = trim($this->getFieldValue($fieldName));
                        if(empty($fieldValue)){
                            $this->errors[] = $errorMessage;	
                        }	

                    break;	
                    case 'compare':

                        $fieldNameCompare = (string) $attributes['field'];
                        if($this->getFieldValue($fieldName) != $this->getFieldValue($fieldNameCompare)){
                            $this->errors[] = $errorMessage;	
                        }

                    break;
                    case 'length':

                        $fieldLength = strlen($this->getFieldValue($fieldName));
                        $min = isset($attributes['min']) ? (int) $attributes['min'] : $fieldLength;
                        $max = isset($attributes['max'])? (int) $attributes['max'] : $fieldLength;

                        if(($fieldLength < $min) || ($fieldLength > $max)){
                            $errorMessage = str_replace('{MIN}', $min,  $errorMessage);
                            $errorMessage = str_replace('{MAX}', $max,  $errorMessage);
                            $this->errors[] = $errorMessage;	
                        }

                    break;
                    case 'regex':

                        $exp = (string) $attributes['exp'];
                        if(!preg_match($exp, $this->getFieldValue($fieldName))){
                            $this->errors[] = $errorMessage;	
                        }

                    break;
                    case 'email':

                        if(!preg_match('/^[a-zA-Z0-9\_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$/', $this->getFieldValue($fieldName))){
                            $this->errors[] = $errorMessage;	
                        }

                    break;
                    case 'range':

                        $fieldValue = $this->getFieldValue($fieldName);
                        $min = isset($attributes['min']) ? $attributes['min'] : $fieldValue;
                        $max = isset($attributes['max'])? $attributes['max'] : $fieldValue;

                        if(($fieldValue < $min) || ($fieldValue > $max)){
                            $errorMessage = str_replace('{MIN}', $min,  $errorMessage);
                            $errorMessage = str_replace('{MAX}', $max,  $errorMessage);
                            $this->errors[] = $errorMessage;	
                        }

                    break;
                    case 'numeric':

                        if(!is_numeric($this->getFieldValue($fieldName))){
                            $this->errors[] = $errorMessage;	
                        }

                    break;
                    case 'date':

                        if(!preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/', $this->getFieldValue($fieldName))){
                            $this->errors[] = $errorMessage;	
                        }

                    break;

                }	

            endforeach;

        endforeach;

    }

    private function getFieldValue($fieldName){
        return isset($this->fields[$fieldName]) ? $this->fields[$fieldName] : NULL;
    }

    public function addError($error){
        $this->errors[] = $error;
    }

    public function hasErrors(){
        return sizeof($this->errors) > 0 ? true : false; 
    }

    public function getErrors(){
        return $this->errors;
    }

    public function getMessages(){
        return $this->messages;
    }

    public function addMessage($message){
        $this->messages[] = $message;
    }

    public function getErrorsAsHtml(){
        return sizeof($this->getErrors()) == 0 ? NULL : '<ul><li>'.implode( '</li><li>', $this->getErrors() ).'</li></ul>';	
    }

    public function jsonResult(){
        $json = array('hasErrors' => $this->hasErrors(), 'errors' => $this->getErrors(), 'messages' => $this->getMessages());
        print json_encode($json);
        exit();
    }

}
?>