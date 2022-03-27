import "./index.scss"
import { TextControl, Flex, FlexBlock, FlexItem, Button, Icon, TextareaControl } from "@wordpress/components"

wp.blocks.registerBlockType("ourplugin/webduel-accordion", {
    title: "Webduel Accordion",
    icon: "smiley",
    category: "common",
    attributes: {
        faqObject: {
            type: "array", default: [
                {
                    question: '',
                    answer: ""
                }
            ]
        }
    },
    edit: EditComponent,
    save: function (props) {
        // get the attributes and show them in front end
        return null
    }
})


function EditComponent(props) {

    function deleteObject(indexToDelete) {
        const newObject = props.attributes.faqObject.filter((x, index) => {
            // check if the current index is not equal to the indexToDelete
            return index != indexToDelete
        })
        props.setAttributes({
            faqObject: newObject
        })
    }
    return (
        <div className="accordion-block-container">
            <Flex>
                <FlexBlock>
                    {/* map the faqObject */}
                    {props.attributes.faqObject.map((item, index) => {
                        return <div className="faq-item" key={index}>
                            <TextControl label="Question:" value={item.question} onChange={newValue => {
                                const newObject = props.attributes.faqObject.concat([])
                                newObject[index].question = newValue
                                newObject[index].answer = item.answer
                                props.setAttributes({ faqObject: newObject })
                            }} />
                            <TextareaControl label="Answer" value={item.answer} onChange={newValue => {
                                const newObject = props.attributes.faqObject.concat([])
                                newObject[index].question = item.question
                                newObject[index].answer = newValue
                                props.setAttributes({ faqObject: newObject })
                            }} />
                            <FlexItem>
                                <Button isLink className="attention-delete" onClick={() => deleteObject(index)}>Delete</Button>
                            </FlexItem>
                        </div>
                    })}
                    {/* {props.attributes.question.map((question, index) => {
                        return <TextControl label="Question:" value={question} onChange={newValue => {
                            const newQuestion = props.attributes.question.concat([])
                            newQuestion[index] = newValue
                            props.setAttributes({ question: newQuestion })
                        }} />
                    })}
                    {props.attributes.answer.map((answer, index) => {
                        return <TextareaControl label="Answer" value={answer} onChange={newValue => {
                            const newAnswer = props.attributes.answer.concat([])
                            newAnswer[index] = newValue
                            props.setAttributes({ answer: newAnswer })
                        }} />

                    })} */}
                </FlexBlock>

            </Flex>
            <Button isPrimary onClick={() => {
                props.setAttributes({
                    faqObject:
                        props.attributes.faqObject.concat([{
                            question: "",
                            answer: ""
                        }])
                })
            }}> Add Another Question</Button>

        </div>
    )
}