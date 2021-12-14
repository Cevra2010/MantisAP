<?php
namespace MantisAP\Objects;

use MantisAP\MantisObject;

class MantisProject extends MantisObject {

    protected $objectName = "projects";
    protected $required = [
        'name',
    ];

    public function hasSubProjects() {
        if(isset($this->fields["subProjects"])) {
            return true;
        }
        return false;
    }

    public function getSubProjects() {
        $subProjectCollection = [];
        if($this->hasSubProjects()) {
            foreach($this->fields["subProjects"] as $subProject) {
                $subProjectCollection[] = MantisProject::find($subProject->id);
            }
        }
        return $subProjectCollection;
    }

    public function hasParentProject() {
        $projects = MantisProject::all();

        foreach($projects as $project) {
            if($project->hasSubProjects()) {
                foreach($project->getSubProjects() as $subProject) {
                    if($subProject->id == $this->id) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
