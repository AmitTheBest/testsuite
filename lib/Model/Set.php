<?php
/**
 * This model reresents a set of tests which usually executed together
 * and may share some sequence, variables etc.
 *
 * Test-files must implement a class descending from Page_Tester, refer
 * to that class for more info. 
 *
 * Multiple Sets present in a single Collection
 */
namespace testsuite;
class Model_Set extends \Model {
    public $dir=null;

    function init(){
        parent::init();

        if(!$this->dir)
            throw $this->exception('Initialize this model through testsuite/Model_Collection');

        $this->addField('name');
        $this->addField('total');
        $this->addField('success');
        $this->addField('fail');
        $this->addField('exception');
        $this->addField('speed');
        $this->addField('memory');
        $this->addField('result');

        $this->setSource('Folder',$this->dir);
        $this->joinSource('Mongo','testsuite_set');
    }
    function skipped(){
        $this['result']='Skipped';
        return $this;
    }
    function runTest(){
        // Extend this method and return skipped() for the tests which
        // you do not want to run
        if (false) {
            return $this->skipped();
        }

        $page='page_'.str_replace('/','_',str_replace('.php','',$this['name']));
        try {
            $p=$this->api->add($page,array('auto_test'=>false));

            if(!$p instanceof Page_Tester){
                $this['result']='Not Supported';
                return;
            }

            if(!$p->proper_responses){
                $this['result']='No proper responses';
                return;
            }

            // This will execute the actual test
            $res=$p->silentTest();

            if($res['skipped']){
                $this['result']='Test was skiped ('.$res['skipped'].')';
                return;
            }


            $this->set($res);
            $this['speed']=round($this['speed'],3);
            //list($this['total_tests'], $this['successful'], $this['time']) = 
            $this['result']=$this['success']==$this['total']?'OK':('FAIL: '.join(', ',$res['failures']));

            $p->destroy();
        }catch(Exception $e){
            $this['fail']='!!';
            $this['result']='Exception: '.($e instanceof BaseException?$e->getText():$e->getMessage());
            return;
        }
    }

    function runTests() {
        $this->each('runTest');
    }
}
