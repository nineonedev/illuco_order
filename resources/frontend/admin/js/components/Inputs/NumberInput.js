import TextInput from "./TextInput";

export default class NumberInput extends TextInput {
    _defineProps(){
        return {
            ...super._defineProps(),
            type: 'number'
        }
    }
}
