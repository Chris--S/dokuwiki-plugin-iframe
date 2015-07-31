<?php
/**
 * Plugin Iframe: Inserts an iframe element to include the specified url
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Christopher Smith <chris@jalakai.co.uk>
 */
 // must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');
 
/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_iframe extends DokuWiki_Syntax_Plugin {
 
    function getInfo(){
      return array(
        'author' => 'Christopher Smith',
        'email'  => 'chris@jalakai.co.uk',
        'date'   => '2008-08-13',
        'name'   => 'Iframe Plugin',
        'desc'   => 'Add an iframe containing the specified url
                     syntax: {{url>http://www.somesite.com/somepage.htm[w,h]|alternate text}}',
        'url'    => 'http://www.dokuwiki.org/plugin:iframe',
      );
    }
 
    function getType() { return 'substition'; }
    function getSort() { return 305; }
    function connectTo($mode) { $this->Lexer->addSpecialPattern('{{url>.*?}}',$mode,'plugin_iframe'); }
    
    function handle($match, $state, $pos, &$handler){
      $match = html_entity_decode(substr($match, 6, -2));
      @list($url, $alt) = explode('|',$match,2);
      $matches=array();
// '/^\s*([^\[|]+)(?:\[(?:([^,\]]*),)?([^,\]]*)\])?(?:\s*(?:\|\s*(.*))?)?$/mD'
      if (preg_match('/(.*)\[(.*)\]$/',trim($url),$matches)) {
        $url = $matches[1];
        if (strpos($matches[2],',') !== false) {
          @list($w, $h) = explode(',',$matches[2],2);
        } else {
          $h = $matches[2];
          $w = '98%';
        }
      } else {
        $w = '98%';
        $h = '400px';
      }
      
      if (!isset($alt)) $alt = '';
      if (!$this->getConf('js_ok') && substr($url,0,11) == 'javascript:') $url = 'error';

      return array(hsc(trim($url)), hsc(trim($alt)), hsc(trim($w)), hsc(trim($h))); 
    }
    
    function render($mode, &$renderer, $data) {
    
      list($url, $alt, $w, $h) = $data;
      if($mode == 'xhtml'){
          if ($url != 'error') {
					  $renderer->doc .= '<p>Click <a href="'.$url.'" alt="'.$alt.'" target="_new">here</a> to open the page in its own window.</p>';
            $renderer->doc .= '<iframe title="'.$alt.'" src="'.$url.'" style="width:'.$w.'; height: '.$h.';">'.$alt.'</iframe>';
          } else {
            $renderer->doc .= '<div>'.$alt.'</div>';
          }
          return true;
      }
      return false;
    }
}