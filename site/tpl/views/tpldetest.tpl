{{test='Salut'}}
{{prenom='Tom'}}
{tpl}var.tpl{/tpl}
{{age}}
<br />
{if cond="notEmpty" param={{test}}}
    test n'est pas vide
{/if}
<br />
<br />
{{i=2}}
{*{{i+=2}}*}
{{i++}}
{{test='Coucou'}}
{{test.= {{prenom}}}}
{loop i={{i}}}
{tpl}var.tpl{/tpl}
    <br />

{/loop}
{{nb=2}}
{{nb2=0}}
{{nb2+={{nb}}}}
{{nb2}}