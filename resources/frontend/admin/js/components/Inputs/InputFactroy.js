import DateInput from './DateInput';
import DateTimeInput from './DateTimeInput';
import NumberInput from "./NumberInput";
import TextInput from "./TextInput";
import SelectInput from "./SelectInput";
import MultiSelectInput from "./MultiSelectInput";
import FileInput from "./FileInput";
import LongTextInput from "./LongTextInput";
import RadioInput from './RadioInput';
import CheckboxGroupInput from './CheckboxGroupInput';
import CounterInput from './CounterInput';
import CountrySelectInput from './CountrySelectInput';
import CheckboxInput from './CheckboxInput';

export default class InputFactory {
    static map = {
        text: {
            label: '텍스트',
            instance: TextInput,
        },
        longText: {
            label: '긴 텍스트',
            instance: LongTextInput,
        },
        number: {
            label: '숫자',
            instance: NumberInput,
        },
        radio: {
            label: '라디오',
            instance: RadioInput,
        },
        checkbox: {
            label: '체크박스',
            instance: CheckboxInput
        },
        'checkbox-group': {
            label: '체크박스',
            instance: CheckboxGroupInput,
        },
        date: {
            label: '날짜',
            instance: DateInput,
        },
        datetime: {
            label: '날짜/시간',
            instance: DateTimeInput,
        },
        select: {
            label: '단일 선택',
            instance: RadioInput,
            // instance: SelectInput,
        },
        'multi-select': {
            label: '다중 선택',
            instance: CheckboxGroupInput,
            // instance: MultiSelectInput,
        },
        counter: {
            label: '수량',
            instance: CounterInput
        },
        'country-select': {
            label: '국가 선택',
            instance: CountrySelectInput,
        },
        file: {
            label: '파일 업로드',
            instance: FileInput,
        },
    };

    /**
     * 타입에 맞는 컴포넌트 클래스 반환
     * @param {string} type 
     * @returns {class} View 클래스
     */
    static make(type) {
        if (type in this.map) {
            return this.map[type].instance;
        }

        console.warn(`[InputFactory] Unknown input type: ${type}. Fallback to TextInput.`);
        return TextInput;
    }

    /**
     * 타입에 맞는 라벨 반환
     * @param {string} type 
     * @returns {string}
     */
    static getLabel(type) {
        return this.map[type]?.label || '텍스트';
    }

    /**
     * 전체 타입 리스트 반환
     */
    static types() {
        return Object.keys(this.map);
    }
}
