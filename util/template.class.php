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
class Template{

    private $dadBlocks = array(); //armazena os blocos pais
    private $childBlocks = array(); //armazena os blocos filhos
    private $tempDadBlock = array(); //armazena temporariamente blocos para localização do pai
    private $blocks = array(); //armazena os blocos encontrados
    private $tpl = NULL; // armazena o template
    private $level = 0; //nivel utilizado para montar arvore de blocos
    private $vars = array(); //variáves a serem substituidas
    private $varsBlock = array(); //variáveis do loop a ser substituída
    private $tempBlock = NULL; //armazena temporarimente um bloco para edição do seu conteúdo
    private $patch = 'template/'; //diretório base dos templates
    private $lastBlock = NULL; //armazena o nome do último bloco utilizado

    /*
    Abre template principal e inicia identificação de includes e blocos dinâmicos
    */
    public function __construct($templateFile = false){ 

        $templateFile = $templateFile == false ? $this->patch.$this->scriptName() : $templateFile;
        $file = file($templateFile) or die ('Template '.$templateFile.' não é um arquivo ou não foi encontrado'); 
        $this->tpl = implode('',$file);

        //$this->identifyIncludes($file);
        $this->identifyBlocks($file);
    }
	
    public function scriptName(){
        $page = end(explode('/', $_SERVER['PHP_SELF']));
        return str_replace('.php','.html', $page);
    }
	
    /*
    Associa valor do bloco a rótulos
    */
    public function __set($label, $value){
        $this->varsBlock[$label] = $value;
    }

    /*
    Associa um template a um rótulo

    public function assignFile($label, $page){
        $file = file($this->patch.$page) or die ('Página '.$page.' não é um arquivo ou não foi encontrada'); 
        $page = implode('',$file);

        $this->tpl = str_replace('{'.$label.'}', $page, $this->tpl);

        //$this->identifyIncludes($file);
        $this->identifyBlocks($file);
    }
    */


    /*
    Associa valor a rótulos
    */
    public function assign($label, $value){
        $this->vars[$label] = $value;
    }

    /*
    Associa valor do bloco a rótulos
    */
    public function assignBlock($label, $value){
        $this->varsBlock[$label] = $value;
    }

    /*
    Varre linha por linha do template para identificar blocos
    */
    private function identifyBlocks($file){
        foreach($file as $line) {
            if(strpos($line, '<!-- include:') !== false) $this->processIncludes($line);
            if(strpos($line, '<!-- ') !== false) $this->extractBlock($line);
        }
    }

    private function processIncludes($line){
        $search = '/<!-- include:(.*) -->/smi';
        if(preg_match($search, $line, $result)){

            $includeFile = str_replace(':','/', $result[1]);

            $file = file_get_contents($includeFile) or die ('Template '.$includeFile.' não encontrado');
            $this->tpl = preg_replace('/<!-- include:'.$result[1].' -->/', $file, $this->tpl);

            $file = file($includeFile) or die ('Template '.$includeFile.' não encontrado');
            $this->identifyBlocks($file);
        }	
    }

    private function extractBlock($line){
        $search = '/<!-- begin_block_(.*) -->/smi';
        if(preg_match($search, $line, $result)){

            /*
            monta árvore
            */
            $this->tempDadBlock[] = $result[1];
            if($this->level == 0){
                /*
                pai procura no documento inteiro
                */
                $this->tempBlock = $this->tpl;
                $block = $this->getBlock($result[1]);
                $this->childBlocks['.'][$result[1]] = $result[1];
                $this->blocks[$result[1]] = $block;
                $this->tempBlock = $block;

                $this->dadBlocks[$result[1]] = '.';

                /*
                substituir local
                */
                $this->tpl = $this->clearBlock($result[1], $this->tpl);

            }else{
                $dad = $this->tempDadBlock[($this->level - 1)];
                $this->blocks[$result[1]] = $this->getBlock($result[1]);

                $this->tempBlock = $this->blocks[$dad];

                /*
                substituir local
                */
                $this->blocks[$dad] = $this->clearBlock($result[1],$this->blocks[$dad]);	
                $this->childBlocks[$dad][$result[1]] =  $result[1];
                $this->dadBlocks[$result[1]] = $dad;
            }
            $this->level++;
        }

        $search = '/<!-- end_block_(.*) -->(.*)/smi';
        if(preg_match($search, $line, $result)){
            array_pop($this->tempDadBlock);
            $this->level--;
        }

    }

    /*
    pega um bloco
    */
    private function getBlock($block){
        $search = '/<!-- begin_block_'.$block.' -->(.*)<!-- end_block_'.$block.' -->/smi';
        preg_match($search, $this->tempBlock, $result) or die ('Bloco '.$block.' n�o encontrado');
        return $result[1];
    }

    /*
    apaga um bloco colocando sua marcação
    */
    private function clearBlock($block, $tpl){
        $search = '/<!-- begin_block_'.$block.' -->(.*)<!-- end_block_'.$block.' -->/smi';
        return preg_replace($search, '<!-- block_'.$block.' -->', $tpl);
    }

    /*
    substitui um bloco por um valor
    */
    public function replaceBlock($name, $replace){
        $this->tpl = str_replace('<!-- block_'.$name.' -->', $replace, $this->tpl);
    }

    /*
    limpa marcações de blocos filhos
    */
    private function clearChild($block){
        if (isset($this->childBlocks[$block])){
            $children = $this->childBlocks[$block];
            foreach($children as $child){
                $this->tpl = str_replace('<!-- block_'.$child.' -->', '', $this->tpl);
            }
        }
    }

    /*
    pega bloco correspodente e substitui variáveis, montando a árvore
    */
    public function block($name){	
        isset($this->blocks[$name]) or die ('Bloco '.$name.' não encontrado');	

        $line = $this->blocks[$name];	
        if(isset($this->varsBlock)){
            foreach($this->varsBlock as $index => $value) {
                $line = str_replace('{'.$index.'}', $value, $line);
            }
        }

        /*
        limpa filhos para não haver troca em local incorreto
        */
        $this->clearChild($name);
        $this->tpl = str_replace('<!-- block_'.$name.' -->', $line.'<!-- block_'.$name.' -->', $this->tpl);
        unset($this->varsBlock);
    }

    /*
    monta e exibe template
    */
    public function show(){
        /*
        associa variáveis
        */
        foreach($this->vars as $index => $value){
            $this->tpl = str_replace('{'.$index.'}', $value, $this->tpl);
        }

        /*
        retira finais de blocos que ficaram em aberto
        */
        $this->tpl =  preg_replace('/<!-- block_(.*) -->/', '', $this->tpl);

        echo $this->tpl;
    }

}
?>