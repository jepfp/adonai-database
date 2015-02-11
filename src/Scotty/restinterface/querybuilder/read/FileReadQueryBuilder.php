<?php
namespace Scotty\restinterface\querybuilder\read;

class FileReadQueryBuilder extends AbstractReadQueryBuilder {
	
    protected function buildCommaSeparatedColumns(){
        return "id, lied_id, filename, filesize, filetype";
    }
    
}

