<?php
// Copyright 2017 Liangzibao, Inc.
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//     http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// See the License for the specific language governing permissions and
// limitations under the License.

namespace LZB\Component;

class Attachments {
    
    private $_attachmentList = array();
    
    public function setAttachment($name, $filepath) {
        if (empty($name)
            && empty(trim($filepath))) {
            return;
        }

        if (function_exists('curl_file_create')) { // php 5.5+
            $uploadFile = curl_file_create($filepath);
        } else {
            $uploadFile = '@'.realpath($filepath);
        }

        $this->_attachmentList[$name] = $uploadFile;
        return;
    }

    public function getAttachment($name) {
        if (isset($this->_attachmentList[$name])) {
            return $this->_attachmentList[$name];
        }

        return false;
    }

    public function getAttachmentList() {
        if (!empty($this->_attachmentList)) {
            return $this->_attachmentList;
        }

        return false;
    }

}
