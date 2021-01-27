<?php
namespace app\forms;

use php\io\IOException;
use php\util\Scanner;
use php\io\Stream;
use php\gui\framework\AbstractForm;
use php\gui\event\UXEvent;
use php\lib\str;
use php\lib\arr;


class MainForm extends AbstractForm
{   
    private $key = 'Петя';
    
    function encodeText(string $a, string $b)
    {
      $binA_arr = str::split((string)decbin(ord($a)), '');
      $binB_arr = str::split((string)decbin(ord($b)), '');
      
      if (count($binA_arr) <= count($binB_arr) ) {
           $binB_arr = array_slice($binB_arr, 0, count($binA_arr));
      } else {
      while (count($binB_arr) < count($binA_arr)) {
          $binB_arr = array_push($binB_arr, '0');
        }    
      }
     
      $result = '';
      for ($i = 0;$i < count($binA_arr);$i++) {
          $result = $result.(int)($binA_arr[$i] || $binB_arr[$i]);
      }
      
      return bindec($result);
    }
 
    
    function decodeText(int $a, string $b)
    {
      $binA_arr = str::split((string)decbin($a), '');
      $binB_arr = str::split((string)decbin(ord($b)), '');
      
      if (count($binA_arr) <= count($binB_arr) ) {
           $binB_arr = array_slice($binB_arr, 0, count($binA_arr));
      } else {
          $binB_arr = array_push($binB_arr, '0');
      }
      
      $result = '';
      for ($i = 0;$i < count($binA_arr);$i++) {
          $result = $result.(int)($binA_arr[$i] || $binB_arr[$i]);
      }
      dump($result);
      return chr(bindec($result));
    }

    /**
     * @event Encode.action 
     */
    function doEncodeAction(UXEvent $event = null)
    {    
        
        $hash_text = $this->textzashifrovat->text;
        $key       = $this->KeyWindow->text;
        
        if (!$key) {
            $key = $this->key;
        }
         
         $hash_text_arr = str::split($hash_text, '');
         $key_arr       = str::split($key, '');
         
         $result = [];
         if (count($hash_text_arr) <= count($key_arr)) {
             for ($i = 0;$i < count($hash_text_arr);$i++) {
                 $result[] = $this->encodeText($hash_text_arr[$i], $key_arr[$i]);
             }
         } else {
             for ($i = 0;floor(count($hash_text_arr)/count($key_arr));$i++) {
                 $key_arr = array_merge($key_arr, $key_arr);
             }
             
             $key_arr = array_slice($key_arr, 0, count($hash_text_arr));
             
              for ($i = 0;$i < count($hash_text_arr);$i++) {
                 $result[] = $this->encodeText($hash_text_arr[$i], $key_arr[$i]);
             }
         }

        $this->textrasshifrovat->text = str::join($result, ' ');
    }

    /**
     * @event Decode.action 
     */
    function doDecodeAction(UXEvent $event = null)
    {    
        $hash_text = $this->textrasshifrovat->text;
        $key       = $this->KeyWindow->text;
        
        if (!$key) {
            $key = $this->key;
        }
        
        $hash_text_arr = str::split($hash_text, ' ');
        $key_arr       = str::split($key, '');
        
        if (count($hash_text_arr) <= count($key_arr)) {
             for ($i = 0;$i < count($hash_text_arr);$i++) {
                 $result[] = $this->decodeText($hash_text_arr[$i], $key_arr[$i]);
             }
        } else {
             for ($i = 0;floor(count($hash_text_arr)/count($key_arr));$i++) {
                 $key_arr = array_merge($key_arr, $key_arr);
             }
             
             $key_arr = array_slice($key_arr, 0, count($hash_text_arr));
             
              for ($i = 0;$i < count($hash_text_arr);$i++) {
                 $result[] = $this->decodeText($hash_text_arr[$i], $key_arr[$i]);
             }
         }
         
         $this->textzashifrovat->text = str::join($result, '');
    }

    /**
     * @event GenerateKey.action 
     */
    function doGenerateKeyAction(UXEvent $e = null)
    {
        $this->KeyWindow->text = 'Саша'; 
    }

    /**
     * @event DownloadFile.action 
     */
    function doDownloadFileAction(UXEvent $e = null)
    {
    try {
       $file = Stream::of('C:\Users\79110\Documents\test.txt');
       $scanner = new Scanner($file);

       $lines = [];

    while ($scanner->hasNextLine()) {
         $line = $scanner->nextLine();
         $lines[] = $line;
    }

    $file->close();
    } catch (IOException $e) {
       alert('Ошибка чтения файла');
    }
    
       $text = '';
    foreach ($lines as $item) {
        $text = $text.$item;
    }
    
       $this->textzashifrovat->text = $text;
  }

    /**
     * @event SaveFile.action 
     */
    function doSaveFileAction(UXEvent $e = null)
    {
    try {
       Stream::putContents('C:\Users\79110\Documents\testResult.txt', $this->textrasshifrovat->text);
       pre('Success');
    } catch (IOException $e) {
       alert('Ошибка записи: ' . $e->getMessage());
    }
  }

}