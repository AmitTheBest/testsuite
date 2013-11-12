<?php
/**
 * This model represents a collection of test-sets. Collection 
 * is usually contributed by a single developer. Typically add-on would
 * come with a single collection of test-sets. As you iterate through this
 * model, it will automatically locate all the test-collections of all
 * the installed add-ons as well as your application.
 */
namespace testsuite;
class Model_Collection extends \Model {

    protected $resource_type='test';
    protected $set_model='testsuite/Model_Set';

    protected $code_coverage_controller = null;

    function init() {
        parent::init();

        $this->addField('name');
        $this->addField('folder');
        $this->addField('lib');

        // cached run statistics?
        $this->addField('last_run');
        $this->addField('pass_cnt');
        $this->addField('fail_cnt');
        $this->addField('skip_cnt');
        $this->addField('exec_time');
        $this->addField('exec_tics');


        // The primary generator of the IDs and
        // basic data is pathfinder. However it can
        // only provide us with the basic location (the ID)
        //
        // Remaining data must be loaded from the secondary table,
        // but it can also be stored there

        $this->setSource('PathFinder',$this->resource_type);

        //$this->addCache('Mongo','testsuite_collection');
    }

    function getTest() {
        if(!$this->loaded())throw $this->exception('Must be loaded');

        $model = $this->add($this->set_model, array());
    }

    /**
     * Will execute all test sets in the collection updating our statistics
     */
    function runTests() {
        $tests = $this->getTests();

        $tests->each(function($m){
            $m->runTests();

            // TODO: update global statistics
        });

        // TODO: save global statistics here

    }

    /**
     * Will execute tests and also record information about code coverage.
     */
    function codeCoverage() {

        if(!$this->controller) throw 
            $this->exception('Coverage controller must be defined');
        
    }


}
