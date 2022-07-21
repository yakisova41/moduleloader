<?php
namespace Yakisova41\ModuleLoader;

class StyleParser
{
    public static function parse(
        $styleJsonString
    )
    {
        $parseResult = [];

        $styleJsonArray = \json_decode($styleJsonString);
        
        foreach($styleJsonArray as $styleJsonSelector => $styles)
        {
            $selectorTokens = self::selectorSplitTokens($styleJsonSelector);
            $parsedSelector = self::parseSelector($selectorTokens);
            
            $parseResult[] = [
                'selector'=>$parsedSelector,
                'styles'=>$styles
            ];
        }

        return $parseResult;
    }

    private static function parseSelector(
        $tokens
    )
    {
        $parsed = [];

        $isUnknownElement = true;
        $prosessingTmp = [];
        $prosessing = [false,''];

        foreach($tokens as $tokenKey => $token)
        {
            if($prosessing[0])
            {
                if(
                    $token['matchType'] !== 'Word' || 
                    $token['matchType'] === 'AttrFinish'||
                    $token['matchType'] === 'PseudoClasses'||
                    $token['matchType'] === 'PseudoElements'||
                    $token['matchType'] === 'HtmlElementFinish' ||
                    $token['matchType'] === 'LoaderStyleOptionFinish' ||
                    array_key_last($tokens) === $tokenKey 
                )
                {
                    if(array_key_last($tokens)  === $tokenKey)
                    {
                        $prosessingTmp[] = $token['rawData'];
                        $isUnknownElement = false;
                    }
                    else{
                        $isUnknownElement = true;
                    }

                    $parsed[] = [
                        'type'=>$prosessing[1],
                        'data'=>implode($prosessingTmp)
                    ];
                    $prosessingTmp = [];
                    $prosessing = [false,''];
                }
                else if($token['matchType'] === 'Word')
                {
                    
                    $prosessingTmp[] = $token['rawData'];
                    
                }
            }

            if($token['matchType'] === 'className')
            {
                $prosessing[0] = true;
                $prosessing[1] = 'className';
                $isUnknownElement = false;
            }
            else if($token['matchType'] === 'IdName')
            {
                $prosessing[0] = true;
                $prosessing[1] = 'IdName';
                $isUnknownElement = false;
            }
            else if($token['matchType'] === 'AttrStart')
            {
                $prosessing[0] = true;
                $prosessing[1] = 'Attr';
                $isUnknownElement = false;
            }
            else if($token['matchType'] === 'PseudoElements')
            {
                $prosessing[0] = true;
                $prosessing[1] = 'PseudoElements';
                $isUnknownElement = false;
            }
            else if($token['matchType'] === 'PseudoClasses')
            {
                $prosessing[0] = true;
                $prosessing[1] = 'PseudoClasses';
                $isUnknownElement = false;
            }
            else if($token['matchType'] === 'AdjacentSibling')
            {
                $parsed[] = [
                    'type'=>'AdjacentSibling'
                ];
                $isUnknownElement = false;
            }
            else if($token['matchType'] === 'GeneralSibling')
            {
                $parsed[] = [
                    'type'=>'GeneralSibling'
                ];
                $isUnknownElement = false;
            }
            else if($token['matchType'] === 'And')
            {
                $parsed[] = [
                    'type'=>'And'
                ];
                $isUnknownElement = false;
            }
            else if($token['matchType'] === 'Child')
            {
                $parsed[] = [
                    'type'=>'Child'
                ];
                $isUnknownElement = false;
            }
            else if($token['matchType'] === 'HtmlElementStart')
            {
                $prosessing[0] = true;
                $prosessing[1] = 'HtmlElement';
                $isUnknownElement = false;
            }

            else if($token['matchType'] === 'LoaderStyleOptionStart')
            {
                $prosessing[0] = true;
                $prosessing[1] = 'LoaderStyleOption';
                $isUnknownElement = false;
            }

        }

        return $parsed;
    }
    
    private static function selectorSplitTokens(
        $selectorString
    )
    {
        $tokens = [];
        
        $selectorChars = str_split($selectorString);
        

        foreach($selectorChars as $selectorCharsKey => $selectorChar)
        {
            if($selectorChar === '.')
            {
                $tokens[] = [
                    'matchType'=>'className'
                ];
            }

            else if($selectorChar === '#')
            {
                $tokens[] = [
                    'matchType'=>'IdName'
                ];
            }

            else if($selectorChar === '+')
            {
                $tokens[] = [
                    'matchType'=>'AdjacentSibling'
                ];
            }

            else if($selectorChar === '~')
            {
                $tokens[] = [
                    'matchType'=>'GeneralSibling'
                ];
            }

            else if($selectorChar === ',')
            {
                $tokens[] = [
                    'matchType'=>'And'
                ];
            }

            else if($selectorChar === '>')
            {
                $tokens[] = [
                    'matchType'=>'Child'
                ];
            }

            else if($selectorChar === '[')
            {
                $tokens[] = [
                    'matchType'=>'AttrStart'
                ];
            }

            else if($selectorChar === ']')
            {
                $tokens[] = [
                    'matchType'=>'AttrFinish'
                ];
            }

            else if($selectorChar === '{')
            {
                $tokens[] = [
                    'matchType'=>'HtmlElementStart'
                ];
            }

            else if($selectorChar === '}')
            {
                $tokens[] = [
                    'matchType'=>'HtmlElementFinish'
                ];
            }

            else if($selectorChar === ':')
            {
                if($tokens[array_key_last($tokens)]['matchType'] === 'PseudoClasses')
                {
                    $tokens[array_key_last($tokens)] = [
                        'matchType'=>'PseudoElements'
                    ];
                }
                else
                {
                    $tokens[] = [
                        'matchType'=>'PseudoClasses'
                    ];
                }
            }

            else if($selectorChar === '?')
            {
                $tokens[] = [
                    'matchType'=>'LoaderStyleOptionStart'
                ];
            }

            else if($selectorChar === '!')
            {
                $tokens[] = [
                    'matchType'=>'LoaderStyleOptionFinish'
                ];
            }

            else if($selectorChar === ' ')
            {
                $tokens[] = [
                    'matchType'=>'Space'
                ];
            }

            else {
                $tokens[] = [
                    'matchType'=>'Word',
                    'rawData'=>$selectorChar
                ];   
            }

        }
        return $tokens;
    }
}