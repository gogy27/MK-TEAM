<?php
require __DIR__ . '/../vendor/autoload.php';


foreach (Nette\Utils\Finder::find('*')->from(__DIR__ . '/../temp/cache')->childFirst() as $entry) {
  $path = (string) $entry;
  if ($entry->isDir()) { // collector: remove empty dirs
    @rmdir($path); // @ - removing dirs is not necessary
  } else {
    if (@unlink($path)) { // @ - file may not already exist
      continue;
    }
    $handle = @fopen($path, 'r+'); // @ - file may not exist
    if ($handle) {
      flock($handle, LOCK_EX);
      ftruncate($handle, 0);
      flock($handle, LOCK_UN);
      fclose($handle);
      @unlink($path); // @ - file may not already exist
    }
  }
}

echo "CACHE has been successfully deleted!!!";
?>