<?php
//Copyright (c) 2011 Lautaro Orazi <orazile@gmail.com>

//Permission is hereby granted, free of charge, to any
//person obtaining a copy of this software and associated
//documentation files (the "Software"), to deal in the
//Software without restriction, including without limitation
//the rights to use, copy, modify, merge, publish,
//distribute, sublicense, and/or sell copies of the
//Software, and to permit persons to whom the Software is
//furnished to do so, subject to the following conditions:

//The above copyright notice and this permission notice
//shall be included in all copies or substantial portions of
//the Software.

//THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY
//KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
//WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
//PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS
//OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR
//OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
//OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
//SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.


class textCutter{
	
	private $endChars = array(" ", ".", ",", ";", ":", "\n");

	public function setChars( $chars =   array(" ", ".", ",", ";", ":", "\n") ){
        if( is_array( $chars ) ){
                $this->endChars = $chars;
        }	
	}
	
	public function getChars(){return $this->endChars;}

	public function get_snippet($search_word, $text, $snippet_max_chars, $two_direction = true, $word_fix = true, $tail = "", $manage_tail = true)
	{
    	//find the position of the search_word into text. if search = 0 => start at position 0, general snippets (very useful)
     	if($search_word != ""){
            $pos = stripos(strtolower(strip_tags($text)), strtolower($search_word));
        }else{
          $pos = 0;
        }    
        //snippet centering the word   ->  $max_chars/2 + keyword + $max_chars/2 else -> keyword + $max_chars
        if($two_direction){
      		//if string in the left < max_chars, start at 0
            if($pos > $snippet_max_chars/2){
                  $pos -= ($snippet_max_chars/2);
            }else{
                $pos = 0;
            }
        }
        
        //if string len < max chars, we dont need word fix.
        $need_word_fix = true;
        if(strlen($text) < $snippet_max_chars){
            $snippet_max_chars = strlen($text);
            $need_word_fix = false;
            if($manage_tail){
                $tail = "";
            }    
        }

        $snippet = substr(strip_tags($text), $pos, $snippet_max_chars);
		//ok we have the snippet, now if fix = true lets fix words
        if($word_fix && $need_word_fix){
      		//first fix the right side
			if( !in_array( $text[$pos + $snippet_max_chars + 1], $this->endChars) || !in_array( $text[$pos + $snippet_max_chars], $this->endChars )	){
				//bad... start looking for the first end char
          		$reverse_pos = strlen($snippet) -1 ;
            	$right_pos = $reverse_pos;
            	while($reverse_pos != 0){
                	if($snippet[$reverse_pos] == " "){
               		$right_pos = $reverse_pos;
                   	break;
                }
                $reverse_pos--;
            }
                $snippet = substr($snippet, 0, $right_pos); 
                  
        	}
            if(($pos - 1) > 0 && ( !in_array($text[$pos - 1], $this-endChars) || !in_array( $text[$pos], $this->endChars ) ) ){ //we have to left side
          	 	$forward_pos = 0;
             	$left_pos = 0;
             	while($forward_pos != strlen($snippet)-1){
            		if($snippet[$forward_pos] == " "){
                		$left_pos = $forward_pos+1;
                        break;
                    }
                    $forward_pos++;
                }
                $snippet = substr($snippet, $left_pos, strlen($snippet)); 
            }
    	}
        return $snippet . $tail;
    }

	public function ext_str_ireplace($findme, $start, $replacewith, $end, $subject)
	{
        // Replaces $findme in $subject with $replacewith
        // Ignores the case and do keep the original capitalization by using $1 in $replacewith
        $rest = $subject;
        $result = '';
         
        while (stripos($rest, $findme) !== false) {
            $pos = stripos($rest, $findme); 
            $replacewith = $start . substr($rest, $pos, strlen($findme))  . $end; 
            // Remove the wanted string from $rest and append it to $result
            $result .= substr($rest, 0, $pos);
            $rest = substr($rest, $pos, strlen($rest)-$pos);
            // Remove the wanted string from $rest and place it correctly into $result
            $result .= str_replace('$1', substr($rest, 0, strlen($findme)), $replacewith);
            $rest = substr($rest, strlen($findme), strlen($rest)-strlen($findme)); 
        }
        // After the last match, append the rest
        $result .= $rest;
        return $result;
	}
}
?>
