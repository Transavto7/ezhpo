<component>
    <!-- R [1..1] Структурированное тело документа -->
    <structuredBody>
        <!-- R [1..1] СЕКЦИЯ: Сведения о документе -->
        <component>
            <section>
                <!-- R [1..1] код секции -->
                <code code="DOCINFO" codeSystem="1.2.643.5.1.13.13.99.2.197" codeSystemVersion="1.7"  codeSystemName="Секции CDA документов" displayName="Сведения о документе"/>
                <!-- R [1..1] заголовок секции -->
                <title>Сведения о документе</title>
                <!-- R [1..1] наполнение секции -->
                <text>
                    <table>
                        <tbody>
                        <tr>
                            <td>
                                <content>Номер медицинской справки</content>
                            </td>
                            <td>
                                <content>855</content>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </text>
                <!-- R [1..1] Номер медицинской справки -->
                <entry>
                    <observation classCode="OBS" moodCode="EVN">
                        <!-- R [1..1] Кодирование позиции номера медицинской справки -->
                        <code code="7000" codeSystem="1.2.643.5.1.13.13.99.2.166" codeSystemVersion="1.23" codeSystemName="Кодируемые поля CDA документов" displayName="Номер медицинской справки"/>
                        <!-- R [1..1] Кодирование номера медицинской справки -->
                        <value xsi:type="ST">855</value>
                    </observation>
                </entry>
            </section>
        </component>
        <!-- R [1..1] СЕКЦИЯ: Место учебы или работы -->
        <component>
            <section>
                <!-- R [1..1] Код секции -->
                <code code="WORK" codeSystem="1.2.643.5.1.13.13.99.2.197" codeSystemVersion="1.7"  codeSystemName="Секции CDA документов" displayName="Место работы и должность, условия труда"/>
                <!-- R [1..1] Заголовок секции -->
                <title>Место работы или учебы</title>
                <!-- [1..1] Наполнение секции -->
                <text>Российский Национальный Исследовательский Медицинский Университет им. Н.И. Пирогова"</text>
                <!-- R [1..1] Кодирование места учебы или работы -->
                <entry>
                    <organizer classCode="CLUSTER" moodCode="EVN">
                        <code code="4073" codeSystem="1.2.643.5.1.13.13.99.2.166" codeSystemVersion="1.23" codeSystemName="Кодируемые поля CDA документов" displayName="Сведения о трудовой деятельности (при осуществлении трудовой деятельности)"/>
                        <statusCode code="completed"/>
                        <!-- [1..1] Место работы -->
                        <participant typeCode="LOC">
                            <!-- R [1..1] Место работы (роль) -->
                            <participantRole classCode="SDLOC">
                                <!-- R [1..1] Адрес места работы -->
                                <addr>
                                    <!-- R [1..1] адрес текстом -->
                                    <streetAddressLine>117513, г. Москва,улица Островитянова, д.1с6.</streetAddressLine>
                                    <!-- R [1..1] Кодирование субъекта РФ (Код ФНС по справочнику "Субъекты Российской Федерации" (OID:1.2.643.5.1.13.13.99.2.206)) -->
                                    <state>77</state>
                                    <!-- [1..1] Кодирование адреса по ФИАС -->
                                    <fias:Address nullFlavor="NI"/>
                                </addr>
                                <!-- R [1..1] Место работы (сущность) -->
                                <playingEntity>
                                    <!-- R [1..1] Место работы (наименование организации) -->
                                    <name>Российский Национальный Исследовательский Медицинский Университет им. Н.И. Пирогова"</name>
                                    <!-- R [1..1] Код ОГРН организации -->
                                    <identity:Ogrn xsi:type="ST">1027739054420</identity:Ogrn>
                                </playingEntity>
                            </participantRole>
                        </participant>
                    </organizer>
                </entry>
            </section>
        </component>
        <!-- R [1..1] Объективные данные и состояние здоровья пациента. Врачи: -терапевт, -хирург, -невролог, -отоларинголог, -офтальмолог и др.-->
        <component>
            <section>
                <!-- R [1..1] Код -->
                <code code="RESCONS" codeSystem="1.2.643.5.1.13.13.99.2.197" codeSystemVersion="1.7" codeSystemName="Секции электронных медицинских документов" displayName="Консультации врачей специалистов"/>
                <!-- R [1..1] Заголовок секции -->
                <title>Объективные данные и состояние здоровья пациента.</title>
                <!-- [1..1] Наполнение секции -->
                <text>
                    <table>
                        <tbody>
                        <tr>
                            <td>
                                <content>Врач-терапевт</content>
                            </td>
                            <td>
                                <content>Здоров</content>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <content>Врач-хирург</content>
                            </td>
                            <td>
                                <content>Здоров</content>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <content>Врач-невролог</content>
                            </td>
                            <td>
                                <content>Обсессивно-компульсивное расстройство</content>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <content>Врач-отоларинголог</content>
                            </td>
                            <td>
                                <content>Здоров</content>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <content>Врач-офтальмолог</content>
                            </td>
                            <td>
                                <content>Здоров</content>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </text>
                <!-- R [5..*] Кодирование консультации -->
                <entry>
                    <observation classCode="OBS" moodCode="EVN">
                        <!-- [1..1] Тип консультации -->
                        <code nullFlavor = "NI"/>
                        <!-- R [1..1] Статус консультации -->
                        <statusCode code="completed"/>
                        <!-- R [1..1] Время выполнения консультации -->
                        <effectiveTime value="20200810955+0300"/>
                        <!-- R [1..1] Текст результатов и\или заключения -->
                        <value xsi:type="ST">Здоров</value>
                        <performer>
                            <assignedEntity>
                                <!-- R [1..1] Уникальный идентификатор автора в МИС -->
                                <id root="1.2.643.5.1.13.13.12.2.77.7823.100.1.1.70" extension="75399"/>
                                <!-- [1..1] СНИЛС автора -->
                                <id nullFlavor="NI"/>
                                <!-- R [1..1] Код должности автора-->
                                <code code="109" codeSystem="1.2.643.5.1.13.13.11.1002"  codeSystemVersion="6.3" codeSystemName="Должности медицинских и фармацевтических работников" displayName="врач-терапевт"/>
                                <!-- R [1..1] АВТОР (человек) -->
                                <assignedPerson>
                                    <!-- R [1..1] Фамилия, Имя, Отчество автора -->
                                    <name>
                                        <!-- R [1..1] Фамилия -->
                                        <family>Иванов</family>
                                        <!-- R [1..1] Имя -->
                                        <given>Захар</given>
                                    </name>
                                </assignedPerson>
                                <!-- R [1..1] Место работы автора  -->
                                <representedOrganization>
                                    <!-- [1..1] Уникальный идентификатор организации -->
                                    <!-- организации - по справочнику «Реестр медицинских организаций Российской Федерации» (OID: 1.2.643.5.1.13.13.11.1461) -->
                                    <!-- индивидуальные предприниматели - указание на отсутствие кода, nullFlavor="OTH" -->
                                    <id nullFlavor= "OTH"/>
                                    <!-- R [1..1] Код ОГРН организации -->
                                    <identity:Ogrn xsi:type="ST">1037734008575</identity:Ogrn>
                                    <!-- R [1..1] Наименование организации -->
                                    <name>Государственное бюджетное учреждение здравоохранения города Москвы "Городская поликлиника № 134 Департамента здравоохранения города Москвы"</name>
                                    <!-- R [1..1] Адрес организации -->
                                    <addr>
                                        <!-- R [1..1] Адрес текстом -->
                                        <streetAddressLine>117574, г. Москва, Новоясеневский проспект, д. 24, корп. 2.</streetAddressLine>
                                        <!-- R [1..1] Кодирование субъекта РФ (Код ФНС по справочнику "Субъекты Российской Федерации" (OID:1.2.643.5.1.13.13.99.2.206)) -->
                                        <state>77</state>
                                        <!-- [1..1] Кодирование адреса по ФИАС -->
                                        <fias:Address nullFlavor="NI"/>
                                    </addr>
                                </representedOrganization>
                            </assignedEntity>
                        </performer>
                        <!-- R [1..1] кодирование ... Шифр по МКБ-10 -->
                        <entryRelationship typeCode="COMP">
                            <observation classCode="OBS" moodCode="EVN">
                                <code code="809" codeSystem="1.2.643.5.1.13.13.99.2.166" codeSystemVersion="1.23" codeSystemName="Кодируемые поля CDA документов" displayName="Шифр по МКБ-10"/>
                                <value xsi:type="CD" code="Z02.0" codeSystem="1.2.643.5.1.13.13.11.1005" codeSystemVersion="2.10" codeSystemName="Международная статистическая классификация болезней и проблем, связанных со здоровьем (10-й пересмотр)" displayName="Обследование в связи с поступлением в учебные заведения">
                                </value>
                            </observation>
                        </entryRelationship>
                    </observation>
                </entry>
                <entry>
                    <observation classCode="OBS" moodCode="EVN">
                        <!-- [1..1] Тип консультации -->
                        <code nullFlavor = "NI"/>
                        <!-- R [1..1] Статус консультации -->
                        <statusCode code="completed"/>
                        <!-- R [1..1] Время выполнения консультации -->
                        <effectiveTime value="20200810955+0300"/>
                        <!-- R [1..1] Текст результатов и\или заключения -->
                        <value xsi:type="ST">Здоров</value>
                        <performer>
                            <assignedEntity>
                                <!-- R [1..1] Уникальный идентификатор автора в МИС -->
                                <id root="1.2.643.5.1.13.13.12.2.77.7823.100.1.1.71" extension="75398"/>
                                <!-- [1..1] СНИЛС автора -->
                                <id nullFlavor="NI"/>
                                <!-- R [1..1] Код должности автора-->
                                <code code="122" codeSystem="1.2.643.5.1.13.13.11.1002"  codeSystemVersion="6.3" codeSystemName="Должности медицинских и фармацевтических работников" displayName="врач-хирург"/>
                                <!-- R [1..1] АВТОР (человек) -->
                                <assignedPerson>
                                    <!-- R [1..1] Фамилия, Имя, Отчество автора -->
                                    <name>
                                        <!-- R [1..1] Фамилия -->
                                        <family>Иванов</family>
                                        <!-- R [1..1] Имя -->
                                        <given>Иван</given>
                                    </name>
                                </assignedPerson>
                                <!-- R [1..1] Место работы автора  -->
                                <representedOrganization>
                                    <!-- [1..1] Уникальный идентификатор организации -->
                                    <!-- организации - по справочнику «Реестр медицинских организаций Российской Федерации» (OID: 1.2.643.5.1.13.13.11.1461) -->
                                    <!-- индивидуальные предприниматели - указание на отсутствие кода, nullFlavor="OTH" -->
                                    <id nullFlavor= "OTH"/>
                                    <!-- R [1..1] Код ОГРН организации -->
                                    <identity:Ogrn xsi:type="ST">1037734008575</identity:Ogrn>
                                    <!-- R [1..1] Наименование организации -->
                                    <name>Государственное бюджетное учреждение здравоохранения города Москвы "Городская поликлиника № 134 Департамента здравоохранения города Москвы"</name>
                                    <!-- R [1..1] Адрес организации -->
                                    <addr>
                                        <!-- R [1..1] Адрес текстом -->
                                        <streetAddressLine>117574, г. Москва, Новоясеневский проспект, д. 24, корп. 2.</streetAddressLine>
                                        <!-- R [1..1] Кодирование субъекта РФ (Код ФНС по справочнику "Субъекты Российской Федерации" (OID:1.2.643.5.1.13.13.99.2.206)) -->
                                        <state>77</state>
                                        <!-- [1..1] Кодирование адреса по ФИАС -->
                                        <fias:Address nullFlavor="NI"/>
                                    </addr>
                                </representedOrganization>
                            </assignedEntity>
                        </performer>
                        <!-- R [1..1] кодирование ... Шифр по МКБ-10 -->
                        <entryRelationship typeCode="COMP">
                            <observation classCode="OBS" moodCode="EVN">
                                <code code="809" codeSystem="1.2.643.5.1.13.13.99.2.166" codeSystemVersion="1.23" codeSystemName="Кодируемые поля CDA документов" displayName="Шифр по МКБ-10"/>
                                <value xsi:type="CD" code="Z02.0" codeSystem="1.2.643.5.1.13.13.11.1005" codeSystemVersion="2.10" codeSystemName="Международная статистическая классификация болезней и проблем, связанных со здоровьем (10-й пересмотр)" displayName="Обследование в связи с поступлением в учебные заведения">
                                </value>
                            </observation>
                        </entryRelationship>
                    </observation>
                </entry>
                <entry>
                    <observation classCode="OBS" moodCode="EVN">
                        <!-- [1..1] Тип консультации -->
                        <code nullFlavor = "NI"/>
                        <!-- R [1..1] Статус консультации -->
                        <statusCode code="completed"/>
                        <!-- R [1..1] Время выполнения консультации -->
                        <effectiveTime value="20200810955+0300"/>
                        <!-- R [1..1] Текст результатов и\или заключения -->
                        <value xsi:type="ST">Обсессивно-компульсивное расстройство</value>
                        <performer>
                            <assignedEntity>
                                <!-- R [1..1] Уникальный идентификатор автора в МИС -->
                                <id root="1.2.643.5.1.13.13.12.2.77.7823.100.1.1.72" extension="75397"/>
                                <!-- [1..1] СНИЛС автора -->
                                <id nullFlavor="NI"/>
                                <!-- R [1..1] Код должности автора-->
                                <code code="45" codeSystem="1.2.643.5.1.13.13.11.1002"  codeSystemVersion="6.3" codeSystemName="Должности медицинских и фармацевтических работников" displayName="врач-невролог"/>
                                <!-- R [1..1] АВТОР (человек) -->
                                <assignedPerson>
                                    <!-- R [1..1] Фамилия, Имя, Отчество автора -->
                                    <name>
                                        <!-- R [1..1] Фамилия -->
                                        <family>Иванов</family>
                                        <!-- R [1..1] Имя -->
                                        <given>Виктор</given>
                                    </name>
                                </assignedPerson>
                                <!-- R [1..1] Место работы автора  -->
                                <representedOrganization>
                                    <!-- [1..1] Уникальный идентификатор организации -->
                                    <!-- организации - по справочнику «Реестр медицинских организаций Российской Федерации» (OID: 1.2.643.5.1.13.13.11.1461) -->
                                    <!-- индивидуальные предприниматели - указание на отсутствие кода, nullFlavor="OTH" -->
                                    <id nullFlavor= "OTH"/>
                                    <!-- R [1..1] Код ОГРН организации -->
                                    <identity:Ogrn xsi:type="ST">1037734008575</identity:Ogrn>
                                    <!-- R [1..1] Наименование организации -->
                                    <name>Государственное бюджетное учреждение здравоохранения города Москвы "Городская поликлиника № 134 Департамента здравоохранения города Москвы"</name>
                                    <!-- R [1..1] Адрес организации -->
                                    <addr>
                                        <!-- R [1..1] Адрес текстом -->
                                        <streetAddressLine>117574, г. Москва, Новоясеневский проспект, д. 24, корп. 2.</streetAddressLine>
                                        <!-- R [1..1] Кодирование субъекта РФ (Код ФНС по справочнику "Субъекты Российской Федерации" (OID:1.2.643.5.1.13.13.99.2.206)) -->
                                        <state>77</state>
                                        <!-- [1..1] Кодирование адреса по ФИАС -->
                                        <fias:Address nullFlavor="NI"/>
                                    </addr>
                                </representedOrganization>
                            </assignedEntity>
                        </performer>
                        <!-- R [1..1] кодирование ... Шифр по МКБ-10 -->
                        <entryRelationship typeCode="COMP">
                            <observation classCode="OBS" moodCode="EVN">
                                <code code="809" codeSystem="1.2.643.5.1.13.13.99.2.166" codeSystemVersion="1.23" codeSystemName="Кодируемые поля CDA документов" displayName="Шифр по МКБ-10"/>
                                <value xsi:type="CD" code="F42" codeSystem="1.2.643.5.1.13.13.11.1005" codeSystemVersion="2.10" codeSystemName="Международная статистическая классификация болезней и проблем, связанных со здоровьем (10-й пересмотр)" displayName="Обсессивно-компульсивное расстройство">
                                </value>
                            </observation>
                        </entryRelationship>
                    </observation>
                </entry>
                <entry>
                    <observation classCode="OBS" moodCode="EVN">
                        <!-- [1..1] Тип консультации -->
                        <code nullFlavor = "NI"/>
                        <!-- R [1..1] Статус консультации -->
                        <statusCode code="completed"/>
                        <!-- R [1..1] Время выполнения консультации -->
                        <effectiveTime value="20200810955+0300"/>
                        <!-- R [1..1] Текст результатов и\или заключения -->
                        <value xsi:type="ST">Здоров</value>
                        <performer>
                            <assignedEntity>
                                <!-- R [1..1] Уникальный идентификатор автора в МИС -->
                                <id root="1.2.643.5.1.13.13.12.2.77.7823.100.1.1.73" extension="75396"/>
                                <!-- [1..1] СНИЛС автора -->
                                <id nullFlavor="NI"/>
                                <!-- R [1..1] Код должности автора-->
                                <code code="53" codeSystem="1.2.643.5.1.13.13.11.1002"  codeSystemVersion="6.3" codeSystemName="Должности медицинских и фармацевтических работников" displayName="врач-оториноларинголог"/>
                                <!-- R [1..1] АВТОР (человек) -->
                                <assignedPerson>
                                    <!-- R [1..1] Фамилия, Имя, Отчество автора -->
                                    <name>
                                        <!-- R [1..1] Фамилия -->
                                        <family>Лапин</family>
                                        <!-- R [1..1] Имя -->
                                        <given>Александр</given>
                                    </name>
                                </assignedPerson>
                                <!-- R [1..1] Место работы автора  -->
                                <representedOrganization>
                                    <!-- [1..1] Уникальный идентификатор организации -->
                                    <!-- организации - по справочнику «Реестр медицинских организаций Российской Федерации» (OID: 1.2.643.5.1.13.13.11.1461) -->
                                    <!-- индивидуальные предприниматели - указание на отсутствие кода, nullFlavor="OTH" -->
                                    <id nullFlavor= "OTH"/>
                                    <!-- R [1..1] Код ОГРН организации -->
                                    <identity:Ogrn xsi:type="ST">1037734008575</identity:Ogrn>
                                    <!-- R [1..1] Наименование организации -->
                                    <name>Государственное бюджетное учреждение здравоохранения города Москвы "Городская поликлиника № 134 Департамента здравоохранения города Москвы"</name>
                                    <!-- R [1..1] Адрес организации -->
                                    <addr>
                                        <!-- R [1..1] Адрес текстом -->
                                        <streetAddressLine>117574, г. Москва, Новоясеневский проспект, д. 24, корп. 2.</streetAddressLine>
                                        <!-- R [1..1] Кодирование субъекта РФ (Код ФНС по справочнику "Субъекты Российской Федерации" (OID:1.2.643.5.1.13.13.99.2.206)) -->
                                        <state>77</state>
                                        <!-- [1..1] Кодирование адреса по ФИАС -->
                                        <fias:Address nullFlavor="NI"/>
                                    </addr>
                                </representedOrganization>
                            </assignedEntity>
                        </performer>
                        <!-- R [1..1] кодирование ... Шифр по МКБ-10 -->
                        <entryRelationship typeCode="COMP">
                            <observation classCode="OBS" moodCode="EVN">
                                <code code="809" codeSystem="1.2.643.5.1.13.13.99.2.166" codeSystemVersion="1.23" codeSystemName="Кодируемые поля CDA документов" displayName="Шифр по МКБ-10"/>
                                <value xsi:type="CD" code="Z02.0" codeSystem="1.2.643.5.1.13.13.11.1005" codeSystemVersion="2.10" codeSystemName="Международная статистическая классификация болезней и проблем, связанных со здоровьем (10-й пересмотр)" displayName="Обследование в связи с поступлением в учебные заведения">
                                </value>
                            </observation>
                        </entryRelationship>
                    </observation>
                </entry>
                <entry>
                    <observation classCode="OBS" moodCode="EVN">
                        <!-- [1..1] Тип консультации -->
                        <code nullFlavor = "NI"/>
                        <!-- R [1..1] Статус консультации -->
                        <statusCode code="completed"/>
                        <!-- R [1..1] Время выполнения консультации -->
                        <effectiveTime value="20200810955+0300"/>
                        <!-- R [1..1] Текст результатов и\или заключения -->
                        <value xsi:type="ST">Здоров</value>
                        <performer>
                            <assignedEntity>
                                <!-- R [1..1] Уникальный идентификатор автора в МИС -->
                                <id root="1.2.643.5.1.13.13.12.2.77.7823.100.1.1.74" extension="75395"/>
                                <!-- [1..1] СНИЛС автора -->
                                <id nullFlavor="NI"/>
                                <!-- R [1..1] Код должности автора-->
                                <code code="54" codeSystem="1.2.643.5.1.13.13.11.1002"  codeSystemVersion="6.3" codeSystemName="Должности медицинских и фармацевтических работников" displayName="врач-офтальмолог"/>
                                <!-- R [1..1] АВТОР (человек) -->
                                <assignedPerson>
                                    <!-- R [1..1] Фамилия, Имя, Отчество автора -->
                                    <name>
                                        <!-- R [1..1] Фамилия -->
                                        <family>Петров</family>
                                        <!-- R [1..1] Имя -->
                                        <given>Александр</given>
                                    </name>
                                </assignedPerson>
                                <!-- R [1..1] Место работы автора  -->
                                <representedOrganization>
                                    <!-- [1..1] Уникальный идентификатор организации -->
                                    <id nullFlavor= "OTH"/>
                                    <!-- R [1..1] Код ОГРН организации -->
                                    <identity:Ogrn xsi:type="ST">1037734008575</identity:Ogrn>
                                    <!-- R [1..1] Наименование организации -->
                                    <name>Государственное бюджетное учреждение здравоохранения города Москвы "Городская поликлиника № 134 Департамента здравоохранения города Москвы"</name>
                                    <!-- R [1..1] Адрес организации -->
                                    <addr>
                                        <!-- R [1..1] Адрес текстом -->
                                        <streetAddressLine>117574, г. Москва, Новоясеневский проспект, д. 24, корп. 2.</streetAddressLine>
                                        <!-- R [1..1] Кодирование субъекта РФ (Код ФНС по справочнику "Субъекты Российской Федерации" (OID:1.2.643.5.1.13.13.99.2.206)) -->
                                        <state>77</state>
                                        <!-- [1..1] Кодирование адреса по ФИАС -->
                                        <fias:Address nullFlavor="NI"/>
                                    </addr>
                                </representedOrganization>
                            </assignedEntity>
                        </performer>
                        <!-- R [1..1] кодирование ... Шифр по МКБ-10 -->
                        <entryRelationship typeCode="COMP">
                            <observation classCode="OBS" moodCode="EVN">
                                <code code="809" codeSystem="1.2.643.5.1.13.13.99.2.166" codeSystemVersion="1.23" codeSystemName="Кодируемые поля CDA документов" displayName="Шифр по МКБ-10"/>
                                <value xsi:type="CD" code="Z02.0" codeSystem="1.2.643.5.1.13.13.11.1005" codeSystemVersion="2.10" codeSystemName="Международная статистическая классификация болезней и проблем, связанных со здоровьем (10-й пересмотр)" displayName="Обследование в связи с поступлением в учебные заведения">
                                </value>
                            </observation>
                        </entryRelationship>
                    </observation>
                </entry>
            </section>
        </component>
        <component>
            <section>
                <!-- R [1..1] код -->
                <code code="RESINSTR" codeSystem="1.2.643.5.1.13.13.99.2.197" codeSystemVersion="1.7" codeSystemName="Секции CDA документов" displayName="Результаты инструментальных исследований"/>
                <!-- R [1..1] заголовок -->
                <title>Данные рентгененологического метода исследования грудной клетки пациента.</title>
                <!-- [1..1] наполнение -->
                <text>
                    <table>
                        <tbody>
                        <tr>
                            <th>Дата</th>
                            <th>Исследование</th>
                            <th>Результаты</th>
                        </tr>
                        <tr>
                            <td>09.08.2020</td>
                            <td>Рентгенография флюорография легких</td>
                            <td>Без патологий</td>
                        </tr>
                        </tbody>
                    </table>
                </text>
                <entry>
                    <!-- R [1..1] Кодирование инстументального исследования (Рентгенография флюорография легких) -->
                    <observation classCode="OBS" moodCode="EVN">
                        <!-- R [1..1]  Тип инструментального исследования -->
                        <code code="7003320" displayName="Рентгенография флюорография легких" codeSystem="1.2.643.5.1.13.13.11.1471"  codeSystemVersion="2.7" codeSystemName="Федеральный справочник инструментальных диагностических исследований"/>
                        <!-- R [1..1]  Статус инструментального исследования -->
                        <statusCode code="completed"/>
                        <!-- R [1..1]  Время выполения инструментального исследования -->
                        <effectiveTime value="20200809"/>
                        <!-- R [1..1] Текст результатов и\или заключения -->
                        <value xsi:type="ST">Без патологий</value>
                    </observation>
                </entry>
            </section>
        </component>
        <component>
            <section>
                <!-- R [1..1] код секции -->
                <code code="RESLAB" codeSystem="1.2.643.5.1.13.13.99.2.197" codeSystemVersion="1.7" codeSystemName="Секции CDA документов" displayName="Результаты лабораторных исследований"/>
                <!-- R [1..1] заголовок секции -->
                <title>Результаты лабораторных исследований.</title>
                <!-- [1..1] наполнение секции -->
                <text>
                    <table>
                        <thead>
                        <tr>
                            <th>Лабораторный тест</th>
                            <th>Значение</th>
                            <th>Единицы измерения</th>
                            <th>Референтный диапазон</th>
                            <th>Дата</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="7">
                                <content styleCode="Bold">Кровь венозная </content>
                            </td>
                        </tr>
                        <tr>
                            <td>Гемоглобин</td>
                            <td>166</td>
                            <td>г/л</td>
                            <td>130 - 160</td>
                            <td>09.08.2020 15:30</td>
                        </tr>
                        </tbody>
                    </table>
                </text>
                <!-- [1..*] Кодирование лабораторного исследования  -->
                <entry>
                    <organizer classCode="CLUSTER" moodCode="EVN">
                        <!-- R [1..1] Кодирование статуса всего исследования -->
                        <statusCode code="completed"/>
                        <!-- [0..1] Ссылка на исходный документ-исследование -->
                        <reference typeCode="REFR">
                            <externalDocument>
                                <id root="1.2.643.5.1.13.13.12.2.77.7973.100.1.1.51" extension="8754869"/>
                            </externalDocument>
                        </reference>
                        <!-- R [1..1] Кодирование группы показателей лабораторного исследования  -->
                        <component>
                            <!-- R [1..1] Группа показателей -->
                            <organizer classCode="BATTERY" moodCode="EVN">
                                <!-- R [1..1] Указание произвольной группировки исследований -->
                                <code>
                                    <originalText>Клинический анализ крови</originalText>
                                </code>
                                <!-- [1..1] Кодирование статуса исследования группы показателей -->
                                <statusCode code="completed"/>
                                <!-- [1..1] Кодирование лабораторного показателя (Гемоглобин)  -->
                                <component>
                                    <observation classCode="OBS" moodCode="EVN">
                                        <!-- R [1..1] Лабораторный показатель -->
                                        <code code="1017128" codeSystem="1.2.643.5.1.13.13.11.1080" codeSystemVersion="3.20" codeSystemName="Федеральный справочник лабораторных исследований. Справочник лабораторных тестов" displayName="Гемоглобин общий, массовая концентрация в крови"/>
                                        <!-- R [1..1] Кодирование статуса исследования показателя -->
                                        <statusCode code="completed"/>
                                        <!-- R [1..1]  Время выполнения исследования показателя -->
                                        <effectiveTime value="202008091530+0300"/>
                                        <!-- R [1..1] Кодирование результата -->
                                        <value xsi:type="PQ" value="166" unit="g/l">
                                            <translation value="166" displayName="г/л" code="60" codeSystem="1.2.643.5.1.13.13.11.1358" codeSystemVersion="2.3" codeSystemName="Единицы измерения"/>
                                        </value>
                                        <!-- [0..1] Кодирование референтного интервала -->
                                        <referenceRange>
                                            <observationRange>
                                                <!-- R [1..1] Описание референтного интервала -->
                                                <text>130 - 160 г/л</text>
                                                <!-- R [1..1] Референтный интервал -->
                                                <value xsi:type="IVL_PQ">
                                                    <low value="130" unit="g/l">
                                                        <translation value="130" displayName="г/л" code="60" codeSystem="1.2.643.5.1.13.13.11.1358" codeSystemVersion="2.3" codeSystemName="Единицы измерения"/>
                                                    </low>
                                                    <high value="160" unit="g/l">
                                                        <translation value="160" displayName="г/л" code="60" codeSystem="1.2.643.5.1.13.13.11.1358" codeSystemVersion="2.3" codeSystemName="Единицы измерения"/>
                                                    </high>
                                                </value>
                                                <!-- R [1..1] Код типа референтного интервала -->
                                                <interpretationCode code="N"/>
                                            </observationRange>
                                        </referenceRange>
                                    </observation>
                                </component>
                            </organizer>
                        </component>
                    </organizer>
                </entry>
            </section>
        </component>
        <component>
            <section>
                <!-- R [1..1] код секции -->
                <code code="RESINFO" codeSystem="1.2.643.5.1.13.13.99.2.197" codeSystemVersion="1.7" codeSystemName="Секции CDA документов" displayName="Заключение"/>
                <!-- R [1..1] заголовок секции -->
                <title>Заключение о профессиональной годности.</title>
                <!-- [1..1] наполнение секции -->
                <text>Годен без ограничений</text>
                <entry>
                    <observation classCode="OBS" moodCode="EVN">
                        <code code="7001" codeSystem="1.2.643.5.1.13.13.99.2.166" codeSystemVersion="1.23" codeSystemName="Кодируемые поля CDA документов" displayName="Профессиональная годность"/>
                        <!-- R [1..1] Кодирование годности -->
                        <value xsi:type="BL" value="true"/>
                        <!-- [0..1] Примечание -->
                        <value xsi:type="ST"></value>
                    </observation>
                </entry>
            </section>
        </component>
    </structuredBody>
</component>
