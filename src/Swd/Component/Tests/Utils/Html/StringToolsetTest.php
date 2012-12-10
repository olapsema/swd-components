<?php

namespace Swd\Component\Tests\Utils\Html;

use Swd\Component\Utils\Html\StringToolset;

class StringToolsetTest extends \PHPUnit_Framework_TestCase
{

    public function testSubstringBlock()
    {

        $html = "<div><p>First, I've identified two limitations of var_export verus serialize.</p><p class='x'>However, I could deal with both of those so I created a benchmark.  I used a single array containing from 10 to 150 indexes.  I've generate the elements' values randomly using booleans, nulls, integers, floats, and some nested arrays (the nested arrays are smaller averaging 5 elements but created similarly).</p><p>The largest percentage of elements are short strings around 10-15 characters.  While there is a small number of long strings (around 500 characters).</p></div>";

        $html = $this->getHtml();

        $tool = new StringToolset();

        $total = $tool->strlen($html);
        $result1 = $tool->substringBlock($html,0,200);

        echo ($result1);
        $pos = $tool->strlen($result1);
        echo "\n---------------------------------------------\n";
        $result2 = $tool->substringBlock($html,$pos,false);
        echo ($result2);


        echo "\n\n";
    }

    public function getHtml()
    {
        $html = '<p>Последний год у меня появился новый метод исчисления времени -  первый день цикла, второй день цикла, третий день цикла… Теперь я знаю о  бесплодии больше, чем участковый врач в какой-нибудь деревне "Зажопинские  выселки". Взять хотя бы пополнение в моем словарном запасе - одна  "инсулинорезистентность" чего стоит! Да, знаю, сама во всем виновата, но  мучительный этап психологических переживаний, который по-умному называется  «пост-абортный синдром», я уже давно прошла. Да, это было. Да, я совершила  ошибку. Прости меня, Господи. И никто не может наказать меня сильнее, чем я  сама. Надо жить дальше. Я лучше буду верить в своеобразную интерпретацию  фаталистической теории, согласно которой событие, которое должно иметь место в  твоей судьбе, рано или поздно случится. <br><br>Прихожу в женскую консультацию в  сто пятый раз, с завистью рассматриваю кругленьких будущих мамочек, которые  поочередно становятся на металлические напольные весы. С такой же завистью  наблюдаю за карапузами на детской площадке. Хочется подойти и начать сюсюкать  "уси-пуси, мой маленький". Не могу понять этот феномен. Даже достаточно циничные  карьеристки не могут удержаться от этих самых "уси-пуси". Наверное, это  называется материнский инстинкт. <br><br>Пытаюсь успокоить себя словами "и тебе  счастье будет, лапочка", иду дальше, анализируя навязчивое желание самой заиметь  такого карапуза. Ведь есть же женщины с напрочь отсутствующим материнским  инстинктом! Например, моя подруга. У нее на детей идиосинкразия, а если говорить  человеческим языком, то обычное «фу, какие они противные». Как мне кажется, это  просто проявление комплекса нелюбимого ребенка. <br><br>Так о чем это я? А, ну  да. Занимаемся самокопанием. А зачем мне вообще нужен ребенок? Признаюсь честно:  помимо хронического обострения материнского инстинкта, который встречается у 90  % женщин репродуктивного возраста, ребенок дает мне абсолютно законное право не  ходить на работу. Готовить мужу вкусные обеды-ужины (завтраки вычеркиваем, я так  рано вставать не могу), стирать и гладить, делать уборку и создавать домашний  уют. Короче говоря, хозяйствовать и делать все то, до чего руки не доходят.  Плохо, что ходить днем в гости к неработающим подружкам не получится. Во-первых,  все они работающие, а во-вторых, вряд ли между материнством и свободным временем  можно поставить знак равенства. <br><br>Это я шучу. Хотя в каждой шутке есть  доля правды. <br><br>Тут меня посещает одна странная мысль. А стоит ли вообще  лечится от бесплодия? Стоит ли бороться с судьбой, пичкая себя разнообразными  лекарствами и быть готовой идти на любые операции? Стоит ли изо дня в день, из  года в год засовывать себе в интересное место градусник и заниматься сексом  только тогда, когда этот самый градусник «дает добро»? Мужчина бы давно опустил  руки. А мы нет, воюем с природой, которая почему-то сказала «нет». Сильный пол,  блин. <br><br>А что если в результате победы мы получим не то, о чем мечтали? У  меня есть по крайней мере три примера, когда столь долгожданный ребенок вырастал  и становился не очень хорошим человеком. Таких детей больше любят, больше холят  и лелеют. В них больше вкладывают и им больше прощают. И когда они в первый раз  посылают свого родителя туда, где не светит солнце, у последнего возникает  удивление, которое идет по цепочке: шок- удивление- обида- разочарование. Нет,  конечно же, нельзя вычеркнуть лет тринадцать, когда ребенок был ребенком и  хлопоты, связанные с материнством-отцовством, были самыми замечательными  хлопотами в мире. А дальше что? Дети уходят, могут даже спасибо не сказать. Это  нормально. Они не обязаны говорить спасибо. Они не просили их рожать, не просили  их баловать и любить. Это всегда выбор родителей. Так стоит ли требовать  благодарности за исполнение своих же желаний? <br><br>И даже когда очередной  доктор подтверждает суровый диагноз - бесплодие, я не сдаюсь. Я хочу, я буду. Я  вспоминаю об огромном количестве брошенных детей, которым нужна моя любовь и  забота. Может сейчас меня останавливает и то, что усыновление-это не покупка  машины, которую можно вернуть, и то, что когда-нибудь на моем тесте могут  появиться две волшебные полоски, я твердо знаю – выход есть всегда. <br><br>Что  тут можно еще добавить? Констатировать, что материнский инстинкт куда сильнее,  чем я раньше думала? И что любая женщина, даже самая неуверенная в себе,  способна горы перевернуть для реализации этой женской миссии? И как бы не  хотелось поскорее погрузиться в повседневную рутину материнства, всегда стоит  помнить, что у тебя не только одно предназначение. Бесконечный мир, куча  неизведанный дорог, прекрасные встречи, познание себя и раскрывание своих  талантов - это тот маяк, который должен освещать твой путь. И еще раз напомню  слова Великого Мастера: все что происходит, происходит только к лучшему!  </p> <strong>Полина Андреева-Скок</strong>';
        return $html;
    }
}
