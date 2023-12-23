<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
                  xmlns:tem="http://tempuri.org/"
                  xmlns:emk="http://schemas.datacontract.org/2004/07/EMKService.Data.Dto">
    <soapenv:Header/>
    <soapenv:Body>
        <tem:UpdatePatient>
            <guid>{{ config('external-system.guid') }}</guid>
            <idLPU>{{ config('external-system.idLPU') }}</idLPU>
            <tem:patient>
                @include('external-system::components.patient')
{{--                <emk:Addresses>--}}
{{--                    <emk:AddressDto>--}}
{{--                        <emk:City>Москва</emk:City>--}}
{{--                        <emk:IdAddressType>2</emk:IdAddressType>--}}
{{--                        <emk:StringAddress>КИРОВА 77-123456</emk:StringAddress>--}}
{{--                    </emk:AddressDto>--}}
{{--                </emk:Addresses>--}}
{{--                <emk:Contacts>--}}
{{--                    <emk:ContactDto>--}}
{{--                        <emk:ContactValue>+79140700077</emk:ContactValue>--}}
{{--                        <emk:IdContactType>1</emk:IdContactType>--}}
{{--                    </emk:ContactDto>--}}
{{--                    <emk:ContactDto>--}}
{{--                        <emk:ContactValue>mail@adenta.pro</emk:ContactValue>--}}
{{--                        <emk:IdContactType>3</emk:IdContactType>--}}
{{--                    </emk:ContactDto>--}}
{{--                    <emk:ContactDto>--}}
{{--                        <emk:ContactValue>123456789012</emk:ContactValue>--}}
{{--                        <emk:IdContactType>4</emk:IdContactType>--}}
{{--                    </emk:ContactDto>--}}
{{--                </emk:Contacts>--}}
{{--                <emk:Documents>--}}
{{--                    <emk:DocumentDto>--}}
{{--                        <emk:DocN>13124241</emk:DocN>--}}
{{--                        <emk:DocS>1234</emk:DocS>--}}
{{--                        <emk:IdDocumentType>14</emk:IdDocumentType>--}}
{{--                        <emk:ProviderName>Паспортный стол</emk:ProviderName>--}}
{{--                        <emk:IssuedDate>2012-11-23T00:00:00</emk:IssuedDate>--}}
{{--                    </emk:DocumentDto>--}}
{{--                </emk:Documents>--}}
            </tem:patient>
        </tem:UpdatePatient>
    </soapenv:Body>
</soapenv:Envelope>
