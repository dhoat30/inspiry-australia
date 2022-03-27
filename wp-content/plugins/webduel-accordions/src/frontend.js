import './frontend.scss'
import React, { useState } from 'react'
import ReactDOM from 'react-dom'

const divsToUpdate = document.querySelectorAll('.webduel-accordion-update-me')

divsToUpdate.forEach((div) => {
    const data = JSON.parse(div.querySelector("pre").innerHTML)
    ReactDOM.render(<Accordion faqArray={data.faqObject} />, div)
    div.classList.remove('webduel-accordion-update-me')
})

function Accordion({ faqArray }) {
    const [selected, setSelected] = useState(null)

    const toggle = (i) => {
        if (selected === i) {
            return setSelected(null)
        }
        setSelected(i)
    }

    return (
        <div className="accordion-frontend">
            {faqArray.map((item, index) => {
                console.log(item)
                return <div key={index} className='item-container'>
                    <div className='question-container' onClick={() => toggle(index)}>
                        <h2>{item.question} </h2>
                        <h2 className='icon'>{selected === index ? "–" : "+"}</h2>
                    </div>
                    <h3
                        className={selected === index ? 'answer show' : 'answer'}
                        dangerouslySetInnerHTML={{ __html: item.answer }} />
                </div>
            })}
        </div >
    )
}