<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="Form086u_XSLT_draft.xsl"?>
<ClinicalDocument xmlns="urn:hl7-org:v3" xmlns:fias="urn:hl7-ru:fias" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:identity="urn:hl7-ru:identity">
    <!-- ЗАГОЛОВОК ДОКУМЕНТА "Медицинская документация. Медицинская справка (врачебное профессионально-консультативное заключение).
        текущий документ был сформирован Утверждена приказом Минздрава России от 15 декабря 2014 г. N 834н." -->
    <!-- Условные обозначения: -->
    <!-- R [1..] Требуемый элемент. Элемент обязан иметь непустое наполнение, nullFlavor не разрешён -->
    <!-- [1..] Обязательный элемент. Элемент обязан присутствовать, но может иметь пустое наполнение с указанием причины отсутствия информации через nullFlavor -->
    <!-- [0..] Не обязательный элемент. Элемент может отсутствовать -->
    <!---->

    @include('medical-reference::components.base')

    <!-- R [1..1]  ДАННЫЕ О ПАЦИЕНТЕ -->
    @include('medical-reference::components.record-target')

    <!-- R [1..1] ДАННЫЕ ОБ АВТОРЕ ДОКУМЕНТА (ВРАЧ-ПРОФПАТОЛОГ, ПОДПИСЫВАЮЩИЙ ДОКУМЕНТ)-->
    @include('medical-reference::components.author')

    <!-- R [1..1] ДАННЫЕ ОБ ОРГАНИЗАЦИИ-ВЛАДЕЛЬЦЕ ДОКУМЕНТА -->
    @include('medical-reference::components.custodian')

    <!-- R [1..1] ДАННЫЕ О ПОЛУЧАТЕЛЕ ДОКУМЕНТА - ИЭМК \ МЗ РФ-->
    @include('medical-reference::components.information-recipient')

    <!-- R [1..1] ДАННЫЕ О ЛИЦЕ, ПРИДАВШЕМ ЮРИДИЧЕСКУЮ СИЛУ ДОКУМЕНТУ -->
    @include('medical-reference::components.legal-authenticator')

    <!-- СВЕДЕНИЯ О СЛУЧАЕ ОКАЗАНИЯ МЕДИЦИНСКОЙ ПОМОЩИ -->
    @include('medical-reference::components.component-of')

    <!-- R [1..1] ТЕЛО ДОКУМЕНТА -->
    @include('medical-reference::components.component')

</ClinicalDocument>
