<?php
class RePEc extends Plugins
{
    public function enableAction(&$context, &$error) {}
    public function disableAction(&$context, &$error) {}

    public function postview(&$context)
    {
        
        if (isset($context['view']['base_rep']['doi'])  && $context['lodeluser']['adminlodel'] == '1') {
            // workaround pour ajouter la balise body qui autrement fait planter le parsing
            View::$page = preg_replace('/(<journal>.*?<\/journal>)/s',"<body>$1</body>",View::$page);

        }
        if ($context['view']['tpl'] == 'edit_entities_edition' && $context['lodeluser']['adminlodel'] == '1') {
            $id = $context['id'];
            $type =$context['type']['type'];
            
            // on n'affiche pas le lien pour un type de document qu'on ne souhaite pas moissonner
            $harvested = $this->_config['harvestedtypes']['value']; 
            if (! preg_match("/$type/",$harvested.'numero')) return;

            $url = './?do=_repec_cook&amp;type='.$type.'&amp;id='.$id;
            $buttons = '<li style="padding-left:40%"><a href='.$url.'>Afficher</a>&nbsp;|&nbsp;<a href="'.$url.'&amp;download=1">Télécharger</a></li>';
            View::$page = preg_replace('/(<h4>Fonctions<\/h4>.*?)(<\/ul>\s*<\/div>)/s','$1<li>Produire un fichier de dépôt RePEc (rdf) :</li>'.$buttons.'$2',View::$page);
        }
    }

    public function cookAction(&$context,&$errors) 
    {
        // données site
        C::set('view.base_rep.doi', 'repec');  
        C::set('doi.prefix', $this->_config['prefix']['value']);

        $harvested = preg_replace('/([a-z]+)/',"'$1'",$this->_config['harvestedtypes']['value']);
        C::set('doi.harvestedtypes', $harvested);

        View::getView()->render('repec');
        return _ajax;
        
    }
}
