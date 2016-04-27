<?php
class ElementFiles extends Element {
	public function edit() {
		$this->app->document->addScript('elements:files/files.js');
		$this->app->document->addStyleSheet('elements:files/files.css');
		$html = array();
		$html[] = '<input type="hidden" class="files_collector_field" name="'.$this->getControlName('files').'" value="'.$this->get('files').'"/>';
		$html[] = '<input type="hidden" class="image_titles_collector_field" name="'.$this->getControlName('titles').'" value="'.$this->get('titles').'"/>';
		$html[] = '<div data-root="'.JURI::root().'" class="files_selector_by_xdsoft">';
		$files = explode('|', $this->get('files'));
		$titles = explode('|', $this->get('titles'));
		if (!count($files)) {
			$files = array('');
		}
		if (!count($titles)) {
			$titles = array('');
		}
        JFormHelper::addFieldPath(dirname(__FILE__));

		$form = new JForm('empty');
		$folder = JFormHelper::loadFieldType('folderlistdeep', true);
        
		$folder->setForm($form);
		$folder->setup(simplexml_load_string('<field accept=".png,.jpg,.doc,.pdf" directory="files" class="image_selector_folder"/>'), null);
		$folder->id = 'xdsoft_image_folder1';
		$folder->name = $this->getControlName('files_folder');
		$folder->value = $this->get('files_folder');
		$html[] = '<p>Выберите папку</p>';
		$html[] = '<div class="files_item">';
		$html[] = $folder->renderField();
		$html[] = '</div>';
		$html[] = '<p>Либо выбрать файлы по отдельности</p>';
		foreach ($files as $i=>$file) {
			$media = JFormHelper::loadFieldType('mediay', true);
			$media->setForm($form);
			$media->setup(simplexml_load_string('<field accept=".png,.jpg,.doc,.pdf" directory="files" class="image_selector_field"/>'), null);
			$media->id = 'xdsoft_image'.$i;
			$media->name = 'xdsoft_image[]';
			$media->value = $file;
			$html[] = '<div class="files_item">';
			$html[] = $media->renderField();
			$html[] = '<input placeholder="Описание" name="xdsoft_image_title[]" type="text" class="image_title_field" value="'.htmlspecialchars($titles[$i]).'">';
			$html[] = '<a class="copy" href="javascript:void(0)">Копировать</a>';
			$html[] = '<a class="delete" href="javascript:void(0)">Удалить</a>';
			$html[] = '</div>';
		}
		$html[] = '</div>';
		return implode("\n", $html);
	}
	public function getFiles() {
        $folder = $this->get('files_folder');
		$titles = $images = array();
        if (!$folder) {
			if ($this->get('files')) {
                $images = explode('|', trim($this->get('files')));
                $titles = explode('|', trim($this->get('titles')));
            }
		} else {
			$dir  = opendir(JPATH_ROOT.'/'.$folder);
			while ($file = readdir($dir)) {
				if (is_file(JPATH_ROOT.'/'.$folder.'/'.$file)) {
					$images[] = $folder.'/'.$file;
					$titles[] = '';
				}
			}
			sort($images);
			closedir($dir);
		}

        return array(
            'files' => $images,
            'titles' => $titles,
        );
    }
	public function render($params = array()) {
       return $this->getFiles()[0];
	}
	public function hasValue($params = array()) {
		$street = $this->get('files');
		return !empty($street);
	}
}